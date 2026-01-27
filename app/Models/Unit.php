<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $guarded = ['id'];

    // Relasi ke Booking
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // --- TAMBAHKAN INI (YANG KURANG) ---
    // Relasi ke RoomType (Unit ini "Milik" Tipe apa?)
    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }
}