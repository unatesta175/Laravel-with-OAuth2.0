<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\Payment;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Get overall statistics
            $stats = $this->getStats();
            
            // Get sales data for chart (last 15 days)
            $salesData = $this->getSalesData();
            
            // Get top services
            $topServices = $this->getTopServices();
            
            // Get appointment status distribution
            $appointmentStatus = $this->getAppointmentStatusDistribution();
            
            // Get recent activity
            $recentActivity = $this->getRecentActivity();

            return response()->json([
                'success' => true,
                'data' => [
                    'stats' => $stats,
                    'salesData' => $salesData,
                    'topServices' => $topServices,
                    'appointmentStatus' => $appointmentStatus,
                    'recentActivity' => $recentActivity,
                ],
                'message' => 'Dashboard data retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve dashboard data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function stats()
    {
        try {
            $stats = $this->getStats();

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Dashboard stats retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve stats: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getStats()
    {
        $now = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();
        $lastWeek = Carbon::now()->subWeek();

        // Upcoming appointments (from today onwards)
        $upcomingAppointments = Booking::where('appointment_date', '>=', $now->format('Y-m-d'))
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        $upcomingAppointmentsLastWeek = Booking::where('appointment_date', '>=', $lastWeek->format('Y-m-d'))
            ->where('appointment_date', '<', $now->format('Y-m-d'))
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        $upcomingTrend = $upcomingAppointmentsLastWeek > 0 
            ? round((($upcomingAppointments - $upcomingAppointmentsLastWeek) / $upcomingAppointmentsLastWeek) * 100, 1)
            : 0;

        // Total appointments this month
        $totalAppointmentsThisMonth = Booking::whereMonth('appointment_date', $now->month)
            ->whereYear('appointment_date', $now->year)
            ->count();

        $totalAppointmentsLastMonth = Booking::whereMonth('appointment_date', $lastMonth->month)
            ->whereYear('appointment_date', $lastMonth->year)
            ->count();

        $totalAppointmentsTrend = $totalAppointmentsLastMonth > 0
            ? round((($totalAppointmentsThisMonth - $totalAppointmentsLastMonth) / $totalAppointmentsLastMonth) * 100, 1)
            : 0;

        // Total revenue this month
        $totalRevenueThisMonth = Payment::where('status', 'paid')
            ->whereMonth('paid_at', $now->month)
            ->whereYear('paid_at', $now->year)
            ->sum('amount');

        $totalRevenueLastMonth = Payment::where('status', 'paid')
            ->whereMonth('paid_at', $lastMonth->month)
            ->whereYear('paid_at', $lastMonth->year)
            ->sum('amount');

        $revenueTrend = $totalRevenueLastMonth > 0
            ? round((($totalRevenueThisMonth - $totalRevenueLastMonth) / $totalRevenueLastMonth) * 100, 1)
            : 0;

        // Total customers this month (unique users who made bookings)
        $totalCustomersThisMonth = Booking::whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->distinct('user_id')
            ->count('user_id');

        $totalCustomersLastMonth = Booking::whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->distinct('user_id')
            ->count('user_id');

        $customersTrend = $totalCustomersLastMonth > 0
            ? round((($totalCustomersThisMonth - $totalCustomersLastMonth) / $totalCustomersLastMonth) * 100, 1)
            : 0;

        return [
            'upcomingAppointments' => [
                'value' => $upcomingAppointments,
                'trend' => $upcomingTrend,
                'isPositive' => $upcomingTrend >= 0,
            ],
            'totalAppointments' => [
                'value' => $totalAppointmentsThisMonth,
                'trend' => $totalAppointmentsTrend,
                'isPositive' => $totalAppointmentsTrend >= 0,
            ],
            'totalRevenue' => [
                'value' => round($totalRevenueThisMonth, 2),
                'trend' => $revenueTrend,
                'isPositive' => $revenueTrend >= 0,
            ],
            'totalCustomers' => [
                'value' => $totalCustomersThisMonth,
                'trend' => $customersTrend,
                'isPositive' => $customersTrend >= 0,
            ],
        ];
    }

    private function getSalesData()
    {
        $days = 15;
        $salesData = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            
            $sales = Payment::where('status', 'paid')
                ->whereDate('paid_at', $date)
                ->sum('amount');

            $salesData[] = [
                'date' => $date,
                'sales' => round($sales, 2),
            ];
        }

        return $salesData;
    }

    private function getTopServices()
    {
        $colors = ['#ff0a85', '#8b5cf6', '#06d6a0', '#f59e0b', '#ef4444', '#3b82f6'];
        
        $topServices = Booking::select('service_id', DB::raw('count(*) as bookings'))
            ->whereMonth('appointment_date', Carbon::now()->month)
            ->whereYear('appointment_date', Carbon::now()->year)
            ->groupBy('service_id')
            ->orderBy('bookings', 'desc')
            ->limit(6)
            ->with('service')
            ->get()
            ->map(function ($booking, $index) use ($colors) {
                return [
                    'name' => $booking->service->name ?? 'Unknown',
                    'bookings' => $booking->bookings,
                    'color' => $colors[$index % count($colors)],
                ];
            });

        return $topServices;
    }

    private function getAppointmentStatusDistribution()
    {
        $total = Booking::whereMonth('appointment_date', Carbon::now()->month)
            ->whereYear('appointment_date', Carbon::now()->year)
            ->count();

        if ($total === 0) {
            return [
                ['name' => 'Completed', 'value' => 0, 'color' => '#10b981'],
                ['name' => 'Upcoming', 'value' => 0, 'color' => '#3b82f6'],
                ['name' => 'Cancelled', 'value' => 0, 'color' => '#ef4444'],
            ];
        }

        $completed = Booking::whereMonth('appointment_date', Carbon::now()->month)
            ->whereYear('appointment_date', Carbon::now()->year)
            ->where('status', 'completed')
            ->count();

        $upcoming = Booking::whereMonth('appointment_date', Carbon::now()->month)
            ->whereYear('appointment_date', Carbon::now()->year)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in', 'checked_out'])
            ->count();

        $cancelled = Booking::whereMonth('appointment_date', Carbon::now()->month)
            ->whereYear('appointment_date', Carbon::now()->year)
            ->whereIn('status', ['cancelled', 'no_show'])
            ->count();

        return [
            [
                'name' => 'Completed',
                'value' => round(($completed / $total) * 100, 1),
                'count' => $completed,
                'color' => '#10b981'
            ],
            [
                'name' => 'Upcoming',
                'value' => round(($upcoming / $total) * 100, 1),
                'count' => $upcoming,
                'color' => '#3b82f6'
            ],
            [
                'name' => 'Cancelled',
                'value' => round(($cancelled / $total) * 100, 1),
                'count' => $cancelled,
                'color' => '#ef4444'
            ],
        ];
    }

    private function getRecentActivity()
    {
        $activities = [];

        // Get recent bookings
        $recentBookings = Booking::with(['client', 'service'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentBookings as $booking) {
            $timeAgo = $this->timeAgo($booking->created_at);
            $clientName = $booking->client->name ?? 'Unknown';
            $serviceName = $booking->service->name ?? 'Unknown';
            
            $activities[] = [
                'type' => 'booking',
                'message' => "New booking: {$clientName} - {$serviceName}",
                'time' => $timeAgo,
                'color' => 'bg-green-100 text-green-600',
                'created_at' => $booking->created_at,
            ];
        }

        // Get recent payments
        $recentPayments = Payment::with('booking.client')
            ->where('status', 'paid')
            ->orderBy('paid_at', 'desc')
            ->limit(3)
            ->get();

        foreach ($recentPayments as $payment) {
            if ($payment->paid_at) {
                $timeAgo = $this->timeAgo($payment->paid_at);
                $clientName = $payment->booking->client->name ?? 'Unknown';
                
                $activities[] = [
                    'type' => 'payment',
                    'message' => "Payment received: RM {$payment->amount} from {$clientName}",
                    'time' => $timeAgo,
                    'color' => 'bg-blue-100 text-blue-600',
                    'created_at' => $payment->paid_at,
                ];
            }
        }

        // Sort by created_at descending and limit to 5
        usort($activities, function($a, $b) {
            return $b['created_at'] <=> $a['created_at'];
        });

        return array_slice($activities, 0, 5);
    }

    private function timeAgo($datetime)
    {
        $now = Carbon::now();
        $diff = $now->diffInMinutes($datetime);

        if ($diff < 1) {
            return 'Just now';
        } elseif ($diff < 60) {
            return $diff . ' minute' . ($diff > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 1440) {
            $hours = floor($diff / 60);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } else {
            $days = floor($diff / 1440);
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        }
    }
}
