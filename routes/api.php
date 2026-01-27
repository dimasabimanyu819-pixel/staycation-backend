<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\BookingController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// --- 1. APARTEMEN (FITUR BARU) ---
// Frontend memanggil ini dulu untuk isi Dropdown Pilihan Apartemen
Route::get('/apartments', [RoomTypeController::class, 'getApartments']);

// --- 2. ROOM TYPES (DAFTAR KAMAR) ---
// Frontend memanggil ini setelah apartemen dipilih (misal: /room-types?apartment_id=1)
Route::get('/room-types', [RoomTypeController::class, 'index']);
Route::get('/room-types/{id}', [RoomTypeController::class, 'show']);

// --- 3. BOOKING & TRANSAKSI (TETAP AMAN) ---
Route::post('/bookings', [BookingController::class, 'store']);
Route::get('/bookings/{id}', [BookingController::class, 'show']);
Route::post('/bookings/{id}/upload-payment', [BookingController::class, 'uploadPayment']);

Route::post('/check-promo', function() {
    return response()->json(['valid' => false, 'discount' => 0]);
});