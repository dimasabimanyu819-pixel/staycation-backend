<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\RoomType;
use App\Models\Unit; 
use App\Models\UnitMaintenance; // <--- Import Model Maintenance
use Carbon\Carbon;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        try {
            // 1. VALIDASI INPUT
            $request->validate([
                'room_type_id'   => 'required|exists:room_types,id',
                'customer_name'  => 'required',
                'customer_phone' => 'required',
                'start_time'     => 'required|date',
                'duration'       => 'required|integer',
                'total_guests'   => 'required|integer',
                'guest_attire'   => 'nullable|string',
            ]);

            // 2. HITUNG WAKTU
            $startTime = Carbon::parse($request->start_time);
            $endTime = $startTime->copy()->addHours((int) $request->duration);

            // 3. LOGIKA OTOMATIS CARI KAMAR KOSONG (ALGORITMA ANTI BENTROK + MAINTENANCE)
            
            // A. Cari Unit yang SEDANG ADA TAMU (Booked)
            $bookedUnitIds = Booking::where('room_type_id', $request->room_type_id)
                ->where('payment_status', '!=', 'cancelled') // Abaikan yang batal
                ->where(function ($query) use ($startTime, $endTime) {
                    // Rumus Cek Bentrok Waktu
                    $query->where('start_time', '<', $endTime)
                          ->where('end_time', '>', $startTime);
                })
                ->pluck('unit_id')
                ->toArray();

            // B. [BARU] Cari Unit yang SEDANG MAINTENANCE (Rusak/Dipakai Owner)
            // Kita pakai full path model atau import di atas
            $maintenanceUnitIds = \App\Models\UnitMaintenance::whereHas('unit', function($q) use ($request){
                    $q->where('room_type_id', $request->room_type_id);
                })
                ->where(function ($query) use ($startTime, $endTime) {
                    // Rumus Cek Bentrok Waktu (Sama dengan booking)
                    $query->where('start_time', '<', $endTime)
                          ->where('end_time', '>', $startTime);
                })
                ->pluck('unit_id')
                ->toArray();
            
            // C. GABUNGKAN DAFTAR UNIT SIBUK (Booked + Maintenance)
            $allUnavailableIds = array_merge($bookedUnitIds, $maintenanceUnitIds);

            // D. Cari Unit yang TERSEDIA (Kecualikan semua yang sibuk)
            $availableUnit = Unit::where('room_type_id', $request->room_type_id)
                ->whereNotIn('id', $allUnavailableIds) 
                ->first(); // Ambil satu yang kosong

            // JIKA TIDAK ADA KAMAR KOSONG
            if (!$availableUnit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maaf, Kamar Penuh atau sedang Maintenance di jam tersebut.'
                ], 422); 
            }

            // 4. HITUNG HARGA
            $roomType = RoomType::findOrFail($request->room_type_id);
            $basePrice = 0;

            switch ((int)$request->duration) {
                case 3: $basePrice = $roomType->price_3_hours ?? 0; break;
                case 6: $basePrice = $roomType->price_6_hours ?? 0; break;
                case 9: $basePrice = $roomType->price_9_hours ?? 0; break;
                case 12: $basePrice = $roomType->price_12_hours ?? $roomType->price ?? 0; break;
                case 24: $basePrice = $roomType->price_24_hours ?? 0; break;
                default: $basePrice = $roomType->price ?? 0; 
            }

            if ($startTime->isWeekend()) {
                $basePrice += ($roomType->weekend_price ?? 0);
            }

            // 5. SIMPAN BOOKING
            $kodeUnik = 'INV-' . strtoupper(Str::random(6));

            $booking = Booking::create([
                'booking_code'   => $kodeUnik,
                'room_type_id'   => $roomType->id,
                'unit_id'        => $availableUnit->id, // ID Unit yang lolos seleksi
                'customer_name'  => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'guest_attire'   => $request->guest_attire,
                'start_time'     => $startTime,
                'end_time'       => $endTime,
                'duration'       => $request->duration,
                'total_guests'   => $request->total_guests,
                'total_price'    => $basePrice,
                'payment_status' => 'pending', 
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Booking Berhasil! Kamar ' . $availableUnit->name . ' telah diamankan.',
                'data' => $booking
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $booking = Booking::with(['roomType', 'unit'])->find($id);
        if (!$booking) return response()->json(['success'=>false, 'message'=>'Not Found'], 404);
        return response()->json(['success'=>true, 'data'=>$booking]);
    }

    public function uploadPayment(Request $request, $id)
    {
        try {
            $request->validate([
                'payment_proof' => 'required|image|max:5120',
            ]);

            $booking = Booking::findOrFail($id);

            if ($request->hasFile('payment_proof')) {
                $path = $request->file('payment_proof')->store('payment-proofs', 'public');
                $booking->update([
                    'payment_proof' => $path,
                    'payment_status' => 'waiting_verification'
                ]);
            }
            return response()->json(['success' => true, 'message' => 'Upload Berhasil']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}