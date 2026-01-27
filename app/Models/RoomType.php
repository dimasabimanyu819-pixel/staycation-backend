<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Tambahkan ini

class RoomType extends Model
{
    use HasFactory;

    /**
     * DAFTAR KOLOM YANG BOLEH DI-EDIT.
     */
    protected $fillable = [
        'apartment_id', // <--- WAJIB DITAMBAHKAN (Agar data apartemen tersimpan)
        'name',
        'description',
        'image',
        'facilities',
        
        // Harga Utama
        'price',
        
        // Harga Paket Durasi
        'price_3_hours',
        'price_6_hours',
        'price_9_hours',
        'price_12_hours',
        'price_24_hours',

        // Harga Weekend
        'weekend_price',   
    ];

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    // --- FITUR BARU: RELASI KE APARTEMEN ---
    public function apartment(): BelongsTo
    {
        return $this->belongsTo(Apartment::class);
    }
}