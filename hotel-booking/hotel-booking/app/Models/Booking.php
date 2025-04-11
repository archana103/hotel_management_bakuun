<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory; 
     protected $fillable = [
        'user_id',  
        'room_id',
        'check_in',
        'check_out',
        'status',
        'total_price'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    
    protected static function boot()
{
    parent::boot();
    
    static::creating(function ($booking) {
        $latestBooking = Booking::latest()->first();
        $nextId = $latestBooking ? $latestBooking->id + 1 : 1;
        $booking->booking_id = 'BKID' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    });
}
}
