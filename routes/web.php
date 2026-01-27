<?php

use Illuminate\Support\Facades\Route;
use App\Models\Booking;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan; // <-- Tambahan Wajib

Route::get('/', function () {
    return view('welcome');
});

// --- JALUR DEBUG & MIGRASI (PENGGANTI UPGRADE LAMA) ---
// Akses browser ke: /run-migration-production
Route::get('/run-migration-production', function () {
    echo "<h1>🔍 Memulai Diagnosa Sistem & Database...</h1>";
    echo "Waktu Server: " . now() . "<br><hr>";

    // 1. CEK KONEKSI DATABASE
    try {
        echo "<h3>1. Mencoba Koneksi ke Database...</h3>";
        
        // Tes koneksi
        DB::connection()->getPdo();
        $dbName = DB::connection()->getDatabaseName();
        
        echo "✅ <strong>KONEKSI BERHASIL!</strong><br>";
        echo "Terhubung ke Database: <span style='color:green; font-weight:bold;'>$dbName</span><br>";
        echo "<hr>";
    } catch (\Exception $e) {
        echo "❌ <strong style='color:red; font-size:18px;'>GAGAL KONEKSI DATABASE:</strong><br>";
        echo "<strong>Pesan Error:</strong> " . $e->getMessage() . "<br><br>";
        echo "👉 <em>Solusi: Cek Environment Variables (DB_HOST, DB_PASSWORD) di Vercel. Pastikan tidak ada typo.</em>";
        die(); // Stop script di sini jika koneksi gagal
    }

    // 2. JALANKAN MIGRASI (BUAT TABEL)
    try {
        echo "<h3>2. Menjalankan Migrasi (Membuat Tabel)...</h3>";
        
        // Perintah ini akan menghapus tabel lama & buat baru (Fresh)
        Artisan::call('migrate:fresh --force');
        
        echo "✅ <strong>MIGRASI SUKSES!</strong> Semua tabel berhasil dibuat.<br>";
        echo "<div style='background:#f4f4f4; padding:10px; border:1px solid #ccc; margin-top:10px;'><pre>" . Artisan::output() . "</pre></div>";
    } catch (\Exception $e) {
        echo "❌ <strong style='color:red;'>GAGAL SAAT MIGRASI:</strong><br>";
        echo "Pesan Error: " . $e->getMessage() . "<br>";
    }
});

// --- FITUR DOWNLOAD LAPORAN (TETAP ADA & AMAN) ---
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