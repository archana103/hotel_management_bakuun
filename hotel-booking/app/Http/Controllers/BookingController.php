<?php

namespace App\Http\Controllers;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Booking::with(['user','room'])->get(), 200);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id'   => 'required|exists:rooms,id',
            'check_in'  => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'user_id'   => 'nullable|exists:users,id', 
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $room = Room::findOrFail($request->room_id);
    
        // Check if the room is available
        $overlap = Booking::where('room_id', $room->id)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($request) {
                $query->whereBetween('check_in', [$request->check_in, $request->check_out])
                      ->orWhereBetween('check_out', [$request->check_in, $request->check_out])
                      ->orWhere(function ($q) use ($request) {
                          $q->where('check_in', '<=', $request->check_in)
                            ->where('check_out', '>=', $request->check_out);
                      });
            })
            ->exists();
    
        if ($overlap) {
            return response()->json(['message' => 'Room is not available for the selected dates'], 400);
        }
    
        // Calculate total price
        $days = Carbon::parse($request->check_in)->diffInDays($request->check_out);
        $totalPrice = $room->price * $days;
    
        // Determine user ID
        $userId = $request->filled('user_id') ? $request->user_id : auth()->id();
    
        // Create the booking
        $booking = Booking::create([
            'user_id'     => $userId,
            'room_id'     => $room->id,
            'check_in'    => $request->check_in,
            'check_out'   => $request->check_out,
            'total_price' => $totalPrice,
            'status'      => 'pending',
        ]);
    
        // Update room status to "booked"
        $room->status = 'booked';
        $room->save();
    
        // Fetch booking summary
        $bookingSummary = Booking::with(['room.hotel'])->find($booking->id);
    
        return response()->json([
            'message' => 'Booking confirmed successfully!',
            'booking' => [
                'booking_id'  => $bookingSummary->booking_id,
                'hotel_name'  => $bookingSummary->room->hotel->name,
                'room_type'   => $bookingSummary->room->room_type,
                'check_in'    => $bookingSummary->check_in,
                'check_out'   => $bookingSummary->check_out,
                'total_price' => $bookingSummary->total_price,
                'status'      => $bookingSummary->status,
            ],
        ], 201);
    }
    
    
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json(Booking::with(['user','room'])->where("user_id",$id)->get(), 200);
    }
    public function show_bookings(string $id)
    {
        $booking = Booking::with(['user', 'room.hotel']) // include hotel if needed
                          ->where('id', $id)
                          ->first();
    
        if (!$booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }
    
        return response()->json($booking, 200);
    }
    public function cancel_bookings(string $id)
    {
        $booking = Booking::find($id);
    
        if (!$booking) {
            return response()->json([
                'message' => 'Booking not found.'
            ], 404);
        }
    
     
        $booking->status = 'cancelled';
        $booking->save();
    
        return response()->json([
            'message' => 'Booking cancelled successfully.',
            'booking' => $booking
        ], 200);
    }
    
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);
    
        $booking->status = $request->status;
        $booking->save();
    
        return response()->json(['message' => 'Booking status updated']);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function getRoomBookings(Room $room)
{
    return $room->bookings()->with('user')->latest()->get();
}
}
