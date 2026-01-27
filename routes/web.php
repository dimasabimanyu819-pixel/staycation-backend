<?php

use Illuminate\Support\Facades\Route;
use App\Models\Booking;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('welcome');
});

// --- 1. FITUR UPGRADE: BUAT TABEL MAINTENANCE (JALANKAN INI) ---
// Akses browser ke: /upgrade-database-maintenance
Route::get('/upgrade-database-maintenance', function () {
    try {
        // Cek apakah tabel sudah ada? Kalau belum, buatkan.
        if (!Schema::hasTable('unit_maintenances')) {
            Schema::create('unit_maintenances', function (Blueprint $table) {
                $table->id();
                // Relasi ke tabel Units (Kalau unit dihapus, jadwal perbaikan ikut terhapus)
                $table->foreignId('unit_id')->constrained('units')->onDelete('cascade'); 
                
                $table->dateTime('start_time'); // Mulai Perbaikan
                $table->dateTime('end_time');   // Selesai Perbaikan
                $table->string('reason')->nullable(); // Alasan (misal: AC Bocor)
                $table->timestamps();
            });
            return "<h1 style='color:green'>SUKSES! Tabel Maintenance Berhasil Dibuat.</h1>";
        }
        return "<h1>Tabel 'unit_maintenances' sudah ada, tidak perlu upgrade lagi.</h1>";
    } catch (\Exception $e) {
        return "<h1 style='color:red'>Error: " . $e->getMessage() . "</h1>";
    }
});

// --- 2. FITUR DOWNLOAD LAPORAN (TETAP ADA & AMAN) ---
Route::get('/export-bookings', function () {
    
    $bookings = Booking::with(['roomType.apartment', 'unit'])->latest()->get();
    $fileName = 'Laporan-Booking-' . date('Y-m-d_H-i') . '.csv';

    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $callback = function() use ($bookings) {
        $file = fopen('php://output', 'w');
        
        // Header
        fputcsv($file, [
            'No', 'Kode Booking', 'Waktu Order', 'Nama Tamu', 'No WA', 
            'Apartemen', 'Tipe Kamar', 'Unit', 'Durasi', 'Total Harga', 'Status', 'Metode Bayar'
        ]);

        // Isi Data
        foreach ($bookings as $index => $row) {
            fputcsv($file, [
                $index + 1,
                $row->booking_code ?? '-',
                $row->created_at->format('d M Y H:i'),
                $row->customer_name,
                $row->customer_phone,
                $row->roomType->apartment->name ?? '-',
                $row->roomType->name ?? '-',
                $row->unit->unit_number ?? 'Belum dipilih',
                $row->duration . ' Jam',
                $row->total_price,
                $row->status,
                $row->payment_proof ? 'Transfer' : 'Cash'
            ]);
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);

})->name('export.bookings');