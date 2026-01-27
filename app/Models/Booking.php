<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',     // <--- WAJIB DITAMBAHKAN
        'room_type_id',     
        'unit_id',          
        'customer_name',
        'customer_phone',
        'guest_attire',     
        'start_time',       
        'end_time',         
        'duration',         
        'total_guests',     
        'total_price',      
        'payment_status',   
        'payment_proof',    
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
        'total_price'=> 'integer',
        'duration'   => 'integer',
    ];

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}