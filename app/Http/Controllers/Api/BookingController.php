<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'Bookings endpoint - coming soon']);
    }

    public function store(Request $request)
    {
        return response()->json(['message' => 'Booking created - coming soon']);
    }

    public function show($id)
    {
        return response()->json(['message' => 'Booking details - coming soon']);
    }

    public function update(Request $request, $id)
    {
        return response()->json(['message' => 'Booking updated - coming soon']);
    }

    public function destroy($id)
    {
        return response()->json(['message' => 'Booking deleted - coming soon']);
    }

    public function reschedule($id, Request $request)
    {
        return response()->json(['message' => 'Booking rescheduled - coming soon']);
    }

    public function cancel($id, Request $request)
    {
        return response()->json(['message' => 'Booking cancelled - coming soon']);
    }

    public function updateStatus($id, Request $request)
    {
        return response()->json(['message' => 'Booking status updated - coming soon']);
    }

    public function getAllBookings()
    {
        return response()->json(['message' => 'All bookings - coming soon']);
    }
}
