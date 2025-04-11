<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
class ReportController extends Controller
{
    public function overviewReport(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');

        // Filter bookings by date range if provided
        $bookings = Booking::when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
            ->where('status', 'confirmed') // ✅ Only confirmed bookings
            ->get();
        ;

        $dailyStats = $bookings->groupBy(function ($booking) {
            return $booking->created_at->format('Y-m-d');
        })->map(function ($group) {
            $confirmed = $group->where('status', 'confirmed');
            return [
                'bookings' => $confirmed->count(),
                'revenue' => $confirmed->sum('total_price'),
                'all_bookings' => $group->count(), // optional
            ];
        });


        return response()->json([
            'daily' => $dailyStats,
            'total_bookings' => $bookings->count(),
            'total_revenue' => $bookings->sum('total_price'),
        ]);
    }

}
