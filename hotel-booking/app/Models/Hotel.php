<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;


    protected $guarded = [];
    public function managers()
    {
        return $this->belongsToMany(User::class, 'hotel_user');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
    public function images() {
        return $this->hasMany(HotelImage::class);
    }

    public function amenities() {
        return $this->belongsToMany(Amenity::class, 'hotel_amenity');
    }
}
