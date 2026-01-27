<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RoomType;
use App\Models\Apartment; // <--- Import Model Apartemen

class RoomTypeController extends Controller
{
    public function index(Request $request)
    {
        try {
            // --- 1. LOGIKA BARU: FILTER APARTEMEN ---
            $query = RoomType::query();

            // Cek apakah Frontend minta apartemen tertentu?
            if ($request->has('apartment_id') && $request->apartment_id != null) {
                $query->where('apartment_id', $request->apartment_id);
            }

            // Ambil data (disertai data apartment-nya)
            $rawRooms = $query->with('apartment')->get();


            // --- 2. LOGIKA LAMA ANDA: BERSIHKAN URL GAMBAR ---
            // Kita olah hasil query di atas menggunakan map() seperti kode Anda sebelumnya
            $rooms = $rawRooms->map(function ($room) {
                
                // --- LOGIKA "BACKEND MENGALAH" (KITA PERTAHANKAN) ---
                $filenameOnly = null;

                if ($room->image) {
                    $raw = $room->image;
                    // Hapus domain & storage kalau ada
                    $clean = str_replace('http://127.0.0.1:8000/storage/', '', $raw);
                    $clean = str_replace('/storage/', '', $clean);
                    $clean = ltrim($clean, '/'); // Hapus garis miring depan
                    
                    $filenameOnly = $clean;
                }
                // ----------------------------------------------------

                return [
                    'id' => $room->id,
                    'name' => $room->name,
                    // Tambahan info apartemen (opsional, biar frontend tau ini kamar di mana)
                    'apartment_name' => $room->apartment->name ?? 'Tidak ada info',
                    
                    // Harga & Gambar (Tetap sesuai settingan Anda)
                    'price_3_hours'  => (int) ($room->price_3_hours ?? 0),
                    'price_6_hours'  => (int) ($room->price_6_hours ?? 0),
                    'price_9_hours'  => (int) ($room->price_9_hours ?? 0),
                    'price_12_hours' => (int) ($room->price_12_hours ?? $room->price ?? 0),
                    'price_24_hours' => (int) ($room->price_24_hours ?? 0),
                    'weekend_price'  => (int) ($room->weekend_price ?? 0),
                    'image' => $filenameOnly, 
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $rooms
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // --- 3. FITUR BARU: AMBIL DAFTAR APARTEMEN ---
    // Frontend butuh ini untuk menampilkan Dropdown Pilihan
    public function getApartments()
    {
        $apartments = Apartment::all();
        return response()->json($apartments);
    }

    // Detail Kamar (Standard)
    public function show($id)
    {
        return RoomType::with(['apartment', 'units'])->find($id);
    }
}