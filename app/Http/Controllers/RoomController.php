<?php

namespace App\Http\Controllers;
use App\Models\Room;
use Illuminate\Http\Request;
use App\Models\Hotel;
use Carbon\Carbon;

use App\Models\Booking;
class RoomController extends Controller
{
    public function index()
{
    $rooms = Room::with('hotel')->get();
    return response()->json($rooms);
}
    public function store(Request $request)
    {
        $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'room_type' => 'required|string',
            'capacity' => 'required|integer',
            'price' => 'required|numeric',
        ]);
    
        $room = Room::create([
            'hotel_id' => $request->hotel_id,
            'room_type' => $request->room_type,
            'capacity' => $request->capacity,
            'price' => $request->price,
            'status' => 'available', 
        ]);
    
        return response()->json($room, 201);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'room_type' => 'required|string',
            'capacity' => 'required|integer',
            'price' => 'required|numeric',
        ]);

        $room = Room::findOrFail($id);
        $room->update([
            'room_type' => $request->room_type,
            'capacity' => $request->capacity,
            'price' => $request->price,
        ]);

        return response()->json($room, 200);
    }
    public function availability($roomId)
{
    $today = Carbon::today();

    $hasActiveBooking = Booking::where('room_id', $roomId)
        ->where('status', 'confirmed')
        ->whereDate('check_in', '<=', $today)
        ->whereDate('check_out', '>', $today)
        ->exists();

    return response()->json([
        'status' => $hasActiveBooking ? 'booked' : 'available'
    ]);
}

}
