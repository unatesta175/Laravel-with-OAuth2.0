<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Get all bookings for the authenticated user
     */
    public function index()
    {
        try {
            $user = Auth::user();

            $bookings = Booking::with(['service.category', 'therapist', 'payment'])
                ->where('user_id', $user->id)
                ->orderBy('appointment_date', 'desc')
                ->orderBy('appointment_time', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $bookings,
                'message' => 'Bookings retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve bookings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new booking
     */
    public function store(Request $request)
    {
        try {
            // Log the incoming request data for debugging
            \Log::info('Booking request data:', $request->all());

            $validator = Validator::make($request->all(), [
                'service_id' => 'required|exists:services,id',
                'therapist_id' => 'required|exists:users,id',
                'appointment_date' => 'required|date|after_or_equal:' . now()->format('Y-m-d'),
                'appointment_time' => 'required|date_format:H:i:s',
                'payment_method' => 'required|in:cash,toyyibpay',
                'notes' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                \Log::error('Booking validation failed:', [
                    'errors' => $validator->errors()->toArray(),
                    'request_data' => $request->all()
                ]);
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed',
                    'debug' => [
                        'received_data' => $request->all(),
                        'validation_rules' => [
                            'service_id' => 'required|exists:services,id',
                            'therapist_id' => 'required|exists:users,id',
                            'appointment_date' => 'required|date|after_or_equal:' . now()->format('Y-m-d'),
                            'appointment_time' => 'required|date_format:H:i:s',
                            'payment_method' => 'required|in:cash,toyyibpay',
                            'notes' => 'nullable|string|max:500'
                        ]
                    ]
                ], 422);
            }

            $user = Auth::user();
            if (!$user) {
                \Log::error('Booking attempt without authentication');
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            \Log::info('Booking attempt by user:', ['user_id' => $user->id, 'user_email' => $user->email]);
            $service = Service::findOrFail($request->service_id);
            $therapist = User::where('id', $request->therapist_id)
                ->where('role', 'therapist')
                ->firstOrFail();

            // Check for overlapping appointments considering service duration
            // Ensure we have clean date and time strings
            $appointmentDate = is_string($request->appointment_date) ? $request->appointment_date : $request->appointment_date->format('Y-m-d');
            $appointmentTime = $request->appointment_time;

            // Clean the time string to ensure it's in H:i:s format
            if (preg_match('/(\d{1,2}):(\d{2})/', $appointmentTime, $matches)) {
                $appointmentTime = sprintf('%02d:%02d:00', $matches[1], $matches[2]);
            }

            \Log::info('Parsing appointment datetime:', [
                'raw_date' => $request->appointment_date,
                'clean_date' => $appointmentDate,
                'raw_time' => $request->appointment_time,
                'clean_time' => $appointmentTime,
                'combined' => $appointmentDate . ' ' . $appointmentTime
            ]);

            try {
                $requestedStartTime = Carbon::createFromFormat('Y-m-d H:i:s', $appointmentDate . ' ' . $appointmentTime);
                $requestedEndTime = $requestedStartTime->copy()->addMinutes($service->duration);
            } catch (\Exception $e) {
                \Log::error('Failed to parse appointment datetime:', [
                    'error' => $e->getMessage(),
                    'date' => $appointmentDate,
                    'time' => $appointmentTime,
                    'combined' => $appointmentDate . ' ' . $appointmentTime
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid date or time format provided'
                ], 400);
            }

            $conflictingBookings = Booking::with('service')
                ->where('therapist_id', $request->therapist_id)
                ->where('appointment_date', $request->appointment_date)
                ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
                ->get()
                ->filter(function ($booking) use ($requestedStartTime, $requestedEndTime) {
                    try {
                        // Ensure clean date format for existing bookings
                        $bookingDate = is_string($booking->appointment_date) ? $booking->appointment_date : $booking->appointment_date->format('Y-m-d');
                        $bookingTime = $booking->appointment_time;

                        // Clean the time string to ensure it's in H:i:s format
                        if (preg_match('/(\d{1,2}):(\d{2})/', $bookingTime, $matches)) {
                            $bookingTime = sprintf('%02d:%02d:00', $matches[1], $matches[2]);
                        }

                        $bookingStartTime = Carbon::createFromFormat('Y-m-d H:i:s', $bookingDate . ' ' . $bookingTime);
                        $bookingEndTime = $bookingStartTime->copy()->addMinutes($booking->service->duration ?? 60);

                        // Check if appointments overlap
                        return $requestedStartTime->lt($bookingEndTime) && $requestedEndTime->gt($bookingStartTime);
                    } catch (\Exception $e) {
                        \Log::error('Failed to parse existing booking datetime:', [
                            'booking_id' => $booking->id,
                            'error' => $e->getMessage(),
                            'date' => $booking->appointment_date,
                            'time' => $booking->appointment_time
                        ]);
                        return false; // Skip this booking if we can't parse it
                    }
                });

            if ($conflictingBookings->isNotEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Therapist is not available at the selected time. The appointment would conflict with an existing booking.'
                ], 409);
            }

            // Create booking
            $booking = Booking::create([
                'user_id' => $user->id,
                'service_id' => $service->id,
                'therapist_id' => $therapist->id,
                'appointment_date' => $appointmentDate,
                'appointment_time' => $appointmentTime,
                'status' => Booking::STATUS_PENDING,
                'notes' => $request->notes,
                'total_amount' => $service->price,
            ]);

            // Create payment record
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'amount' => $service->price,
                'status' => Payment::STATUS_UNPAID,
                'payment_method' => $request->payment_method,
            ]);

            // Handle payment methods
            if ($request->payment_method === 'cash') {
                // Cash payment - keep as pending (will be paid at the spa)
                // Payment status remains as pending until staff confirms payment

                $booking->load(['service.category', 'therapist', 'payment']);

                return response()->json([
                    'success' => true,
                    'data' => $booking,
                    'message' => 'Booking created successfully! Please pay at the spa.'
                ], 201);

            } elseif ($request->payment_method === 'toyyibpay') {
                \Log::info('Processing ToyyibPay payment for booking:', ['booking_id' => $booking->id]);

                // ToyyibPay integration - Production Mode
                // ToyyibPay expects billAmount in sen. Force RM1 (100 sen) for now.
                // If you want to use the real price, replace with:
                // $amountInSen = (int) round(((float) $service->price) * 100);
                $amountInSen = 100;

                // Safer local-date string for description
                $appointmentLocalDate = $booking->appointment_date instanceof \Carbon\Carbon
                    ? $booking->appointment_date->format('Y-m-d')
                    : (string) $booking->appointment_date;

                // Get proper backend URL (with port if localhost)
                $backendUrl = env('APP_URL', 'http://localhost:8000');

                $toyyibPayData = [
                    'userSecretKey' => env('TOYYIBPAY_SECRET_KEY'),
                    'categoryCode' => env('TOYYIBPAY_CATEGORY_CODE'),
                    'billName' => 'BOOKING-' . $booking->id,
                    'billDescription' => 'Spa booking for ' . $service->name . ' on ' . $appointmentLocalDate,
                    'billPriceSetting' => 1,
                    'billPayorInfo' => 1,
                    'billAmount' => $amountInSen, // amount in sen
                    'billReturnUrl' => $backendUrl . '/api/toyyibpay/callback',
                    'billCallbackUrl' => $backendUrl . '/api/toyyibpay/callback',
                    'billExternalReferenceNo' => 'SPA-BOOKING-' . $booking->id . '-' . time(),
                    'billTo' => $user->name,
                    'billEmail' => $user->email,
                    'billPhone' => $user->phone ?? '0123456789',
                    'billSplitPayment' => 0,
                    'billPaymentChannel' => 0, // 0 = FPX
                ];

                        try {
                            \Log::info('=== TOYYIBPAY DEBUG START ===');
                            \Log::info('Creating ToyyibPay bill with data:', $toyyibPayData);
                            \Log::info('App URL: ' . config('app.url'));

                            // Create bill with ToyyibPay Production API (ToyyibPay expects form-encoded payload)
                            $response = \Http::asForm()->timeout(30)->post('https://toyyibpay.com/index.php/api/createBill', $toyyibPayData);

                            \Log::info('ToyyibPay API response:', [
                                'status' => $response->status(),
                                'body' => $response->body(),
                                'successful' => $response->successful(),
                                'headers' => $response->headers()
                            ]);

                            if ($response->successful()) {
                                $responseData = $response->json();

                                \Log::info('ToyyibPay response data parsed:', [
                                    'raw_response' => $response->body(),
                                    'parsed_data' => $responseData,
                                    'is_array' => is_array($responseData),
                                    'response_type' => gettype($responseData)
                                ]);

                                // Check different possible response formats
                                $billCode = null;

                                if (is_array($responseData) && isset($responseData[0]['BillCode'])) {
                                    // Standard array format
                                    $billCode = $responseData[0]['BillCode'];
                                } elseif (is_array($responseData) && isset($responseData['BillCode'])) {
                                    // Direct object format
                                    $billCode = $responseData['BillCode'];
                                } elseif (is_string($responseData)) {
                                    // Sometimes ToyyibPay returns just the bill code as string
                                    $billCode = $responseData;
                                }

                                \Log::info('Extracted bill code:', ['bill_code' => $billCode]);

                                if ($billCode) {
                                    $payment->update([
                                        'toyyibpay_transaction_id' => $billCode,
                                    ]);

                                    $booking->load(['service.category', 'therapist', 'payment']);

                                    $paymentUrl = 'https://toyyibpay.com/' . $billCode;

                                    \Log::info('SUCCESS: Returning payment URL:', ['payment_url' => $paymentUrl]);

                                    return response()->json([
                                        'success' => true,
                                        'data' => $booking,
                                        'payment_url' => $paymentUrl,
                                        'message' => 'Booking created successfully! Redirecting to payment...'
                                    ], 201);
                                } else {
                                    // Log if BillCode is missing
                                    \Log::error('ToyyibPay response missing BillCode in all formats:', [
                                        'raw_response' => $response->body(),
                                        'parsed_data' => $responseData
                                    ]);
                                }
                            } else {
                                // Log HTTP error details
                                \Log::error('ToyyibPay API HTTP error:', [
                                    'status' => $response->status(),
                                    'body' => $response->body(),
                                    'headers' => $response->headers()
                                ]);
                            }

                            // If ToyyibPay fails, log error but still create booking
                            \Log::error('ToyyibPay bill creation failed - falling back:', [
                                'response_status' => $response->status(),
                                'response_body' => $response->body(),
                                'booking_id' => $booking->id
                            ]);

                        } catch (\Exception $e) {
                            \Log::error('=== TOYYIBPAY EXCEPTION ===');
                            \Log::error('ToyyibPay API exception:', [
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString(),
                                'booking_id' => $booking->id,
                                'toyyibpay_data' => $toyyibPayData
                            ]);
                        }

                        \Log::info('=== TOYYIBPAY DEBUG END ===');

                // Fallback: booking created but payment pending
                $booking->load(['service.category', 'therapist', 'payment']);

                return response()->json([
                    'success' => true,
                    'data' => $booking,
                    'message' => 'Booking created successfully! Payment gateway temporarily unavailable, please pay at the spa.'
                ], 201);
            }

            // Default response
            $booking->load(['service.category', 'therapist', 'payment']);

            return response()->json([
                'success' => true,
                'data' => $booking,
                'message' => 'Booking created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create booking: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle ToyyibPay callback
     */
    public function toyyibpayCallback(Request $request)
    {
        try {
            \Log::info('ToyyibPay callback received:', $request->all());

            $billCode = $request->input('billcode');
            $statusId = $request->input('status_id');
            $transactionId = $request->input('transaction_id');
            $orderNumber = $request->input('order_id');

            // Frontend URL
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');

            if (!$billCode) {
                \Log::error('ToyyibPay callback missing bill code');
                return redirect($frontendUrl . '/my-bookings?payment=error&message=Missing bill code');
            }

            // Find payment by ToyyibPay transaction ID
            $payment = Payment::where('toyyibpay_transaction_id', $billCode)->first();

            if (!$payment) {
                \Log::error('Payment not found for ToyyibPay callback:', ['billcode' => $billCode]);
                return redirect($frontendUrl . '/my-bookings?payment=error&message=Payment record not found');
            }

            // Update payment status based on ToyyibPay response
            if ($statusId == '1') {
                // Payment successful
                $payment->update([
                    'status' => Payment::STATUS_PAID,
                    'paid_at' => now(),
                ]);

                // Update booking status to confirmed when ToyyibPay payment is successful
                $booking = $payment->booking;
                if ($booking && $booking->status === Booking::STATUS_PENDING) {
                    $booking->update([
                        'status' => Booking::STATUS_CONFIRMED,
                    ]);
                    \Log::info('Booking confirmed after successful payment:', ['booking_id' => $booking->id]);
                }

                \Log::info('Payment marked as paid:', ['payment_id' => $payment->id, 'billcode' => $billCode]);

                // Redirect to frontend with success message
                return redirect($frontendUrl . '/my-bookings?payment=success&message=Payment successful! Your booking is confirmed.');
            } else {
                // Payment failed or pending
                \Log::info('Payment failed or pending:', ['payment_id' => $payment->id, 'status_id' => $statusId]);

                // Redirect to frontend with error message
                return redirect($frontendUrl . '/my-bookings?payment=failed&message=Payment was not successful. Please try again.');
            }

        } catch (\Exception $e) {
            \Log::error('ToyyibPay callback error:', ['error' => $e->getMessage(), 'request' => $request->all()]);

            $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');
            return redirect($frontendUrl . '/my-bookings?payment=error&message=An error occurred while processing your payment');
        }
    }

    /**
     * Get a specific booking
     */
    public function show($id)
    {
        try {
            $user = Auth::user();

            $booking = Booking::with(['service.category', 'therapist', 'payment'])
                ->where('id', $id)
                ->where('user_id', $user->id)
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $booking,
                'message' => 'Booking retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }
    }

    /**
     * Update a booking (not implemented for security)
     */
    public function update(Request $request, $id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Direct booking updates not allowed. Use cancel endpoint.'
        ], 403);
    }

    /**
     * Delete a booking (not implemented for security)
     */
    public function destroy($id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Direct booking deletion not allowed. Use cancel endpoint.'
        ], 403);
    }

    /**
     * Cancel a booking
     */
    public function cancel($id, Request $request)
    {
        try {
            $user = Auth::user();
            
            // Admin and therapist can cancel any booking
            // Clients can only cancel their own bookings
            if (in_array($user->role, ['admin', 'therapist'])) {
                $booking = Booking::findOrFail($id);
            } else {
                $booking = Booking::where('id', $id)
                    ->where('user_id', $user->id)
                    ->firstOrFail();
            }

            if (!$booking->canBeCancelled()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This booking cannot be cancelled'
                ], 400);
            }

            $cancelledBy = $user->role === 'client' ? 'client' : 'staff';
            $booking->update([
                'status' => Booking::STATUS_CANCELLED,
                'notes' => $booking->notes . "\n\nCancellation reason: " . ($request->reason ?? 'No reason provided') . " (Cancelled by {$cancelledBy})"
            ]);

            $booking->load(['service.category', 'therapist', 'payment']);

            return response()->json([
                'success' => true,
                'data' => $booking,
                'message' => 'Booking cancelled successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel booking: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update booking status (admin/therapist only)
     */
    public function updateStatus($id, Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:pending,confirmed,checked_in,checked_out,completed,cancelled,no_show'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed'
                ], 422);
            }

            $user = Auth::user();
            if (!in_array($user->role, ['admin', 'therapist'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to update booking status'
                ], 403);
            }

            $booking = Booking::findOrFail($id);

            // If therapist, can only update their own bookings
            if ($user->role === 'therapist' && $booking->therapist_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only update your own bookings'
                ], 403);
            }

            $booking->update(['status' => $request->status]);
            $booking->load(['service.category', 'therapist', 'client', 'payment']);

            return response()->json([
                'success' => true,
                'data' => $booking,
                'message' => 'Booking status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update booking status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all bookings (admin and therapist)
     */
    public function getAllBookings()
    {
        try {
            $user = Auth::user();
            if (!in_array($user->role, ['admin', 'therapist'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Therapists can see all bookings (or you can filter by therapist_id for their own bookings)
            $query = Booking::with(['service.category', 'therapist', 'client', 'payment']);

            // If you want therapists to see only their own bookings, uncomment this:
            // if ($user->role === 'therapist') {
            //     $query->where('therapist_id', $user->id);
            // }

            $bookings = $query->orderBy('appointment_date', 'desc')
                ->orderBy('appointment_time', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $bookings,
                'message' => 'All bookings retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve bookings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate receipt for a booking
     */
    public function generateReceipt($id)
    {
        try {
            $user = Auth::user();
            $booking = Booking::with(['service.category', 'therapist', 'payment', 'client'])
                ->where('id', $id)
                ->where('user_id', $user->id)
                ->firstOrFail();

            if (!$booking->payment || $booking->payment->status !== 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Receipt not available for unpaid bookings'
                ], 400);
            }

            // Return HTML receipt view
            return view('receipt', ['booking' => $booking]);

        } catch (\Exception $e) {
            \Log::error('Receipt generation error:', [
                'error' => $e->getMessage(),
                'booking_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate receipt: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retry payment for an unpaid booking
     */
    public function retryPayment($id)
    {
        try {
            $user = Auth::user();
            $booking = Booking::with(['service.category', 'therapist', 'payment'])
                ->where('id', $id)
                ->where('user_id', $user->id)
                ->firstOrFail();

            if (!$booking->payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment record not found for this booking'
                ], 404);
            }

            if ($booking->payment->status === Payment::STATUS_PAID) {
                return response()->json([
                    'success' => false,
                    'message' => 'This booking has already been paid'
                ], 400);
            }

            if ($booking->status === 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot retry payment for a cancelled booking'
                ], 400);
            }

            if ($booking->payment->payment_method !== 'toyyibpay') {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment retry is only available for ToyyibPay payments'
                ], 400);
            }

            // If ToyyibPay transaction ID exists, generate payment URL from existing bill code
            if ($booking->payment->toyyibpay_transaction_id) {
                $paymentUrl = 'https://toyyibpay.com/' . $booking->payment->toyyibpay_transaction_id;

                return response()->json([
                    'success' => true,
                    'payment_url' => $paymentUrl,
                    'message' => 'Payment link retrieved successfully'
                ]);
            }

            // Otherwise, create a new ToyyibPay bill
            $service = $booking->service;
            $amountInSen = 100; // RM1 for testing, or use: (int) round(((float) $service->price) * 100);

            $appointmentLocalDate = $booking->appointment_date instanceof \Carbon\Carbon
                ? $booking->appointment_date->format('Y-m-d')
                : (string) $booking->appointment_date;

            // Get proper backend URL (with port if localhost)
            $backendUrl = env('APP_URL', 'http://localhost:8000');

            $toyyibPayData = [
                'userSecretKey' => env('TOYYIBPAY_SECRET_KEY'),
                'categoryCode' => env('TOYYIBPAY_CATEGORY_CODE'),
                'billName' => 'BOOKING-' . $booking->id,
                'billDescription' => 'Spa booking for ' . $service->name . ' on ' . $appointmentLocalDate,
                'billPriceSetting' => 1,
                'billPayorInfo' => 1,
                'billAmount' => $amountInSen,
                'billReturnUrl' => $backendUrl . '/api/toyyibpay/callback',
                'billCallbackUrl' => $backendUrl . '/api/toyyibpay/callback',
                'billExternalReferenceNo' => 'SPA-BOOKING-' . $booking->id . '-RETRY-' . time(),
                'billTo' => $user->name,
                'billEmail' => $user->email,
                'billPhone' => $user->phone ?? '0123456789',
                'billSplitPayment' => 0,
                'billPaymentChannel' => 0,
            ];

            try {
                $response = \Http::asForm()->timeout(30)->post('https://toyyibpay.com/index.php/api/createBill', $toyyibPayData);

                if ($response->successful()) {
                    $responseData = $response->json();
                    $billCode = null;

                    if (is_array($responseData) && isset($responseData[0]['BillCode'])) {
                        $billCode = $responseData[0]['BillCode'];
                    } elseif (is_array($responseData) && isset($responseData['BillCode'])) {
                        $billCode = $responseData['BillCode'];
                    } elseif (is_string($responseData)) {
                        $billCode = $responseData;
                    }

                    if ($billCode) {
                        // Update payment record with new bill code
                        $booking->payment->update([
                            'toyyibpay_transaction_id' => $billCode,
                        ]);

                        $paymentUrl = 'https://toyyibpay.com/' . $billCode;

                        return response()->json([
                            'success' => true,
                            'payment_url' => $paymentUrl,
                            'message' => 'Payment link generated successfully'
                        ]);
                    }
                }

                \Log::error('ToyyibPay retry payment failed:', [
                    'booking_id' => $booking->id,
                    'response' => $response->body()
                ]);

            } catch (\Exception $e) {
                \Log::error('ToyyibPay retry payment exception:', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage()
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment gateway temporarily unavailable. Please try again later.'
            ], 503);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retry payment: ' . $e->getMessage()
            ], 500);
        }
    }
}
