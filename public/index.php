<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. Cek Maintenance Mode
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// 2. Load Autoload
require __DIR__.'/../vendor/autoload.php';

// 3. Booting Aplikasi
$app = require_once __DIR__.'/../bootstrap/app.php';

// --- BAGIAN KUNCI UNTUK VERCEL ---
// Kita paksa Laravel memakai folder /tmp untuk semua file cache/log/view
// karena folder asli di Vercel itu READ-ONLY (Tidak bisa ditulisi).
$storagePath = '/tmp/storage';
$app->useStoragePath($storagePath);

// Buat struktur folder di dalam /tmp jika belum ada
if (!is_dir($storagePath)) {
    mkdir($storagePath, 0777, true);
    $folders = [
        '/framework/views',
        '/framework/cache/data',
        '/framework/sessions',
        '/logs'
    ];
    foreach ($folders as $folder) {
        if (!is_dir($storagePath . $folder)) {
            mkdir($storagePath . $folder, 0777, true);
        }
    }
}
// ---------------------------------

// 4. Jalankan Aplikasi dengan JEBAKAN ERROR
try {
    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    // Kalau error lagi, kita paksa cetak error ASLINYA ke layar
    // supaya tidak muncul "Target class view does not exist" lagi.
    echo "<div style='background: #f8d7da; color: #721c24; padding: 20px; font-family: sans-serif;'>";
    echo "<h1>🔥 Error Tertangkap!</h1>";
    echo "<h3>Pesan Error Asli:</h3>";
    echo "<pre style='background: #fff; padding: 15px; border: 1px solid #ccc;'>" . $e->getMessage() . "</pre>";
    echo "<h3>Lokasi File:</h3>";
    echo "<p>" . $e->getFile() . " di baris " . $e->getLine() . "</p>";
    echo "</div>";
    die();
}