<?php

// 1. Paksa Munculkan Error ke Layar (Biar tidak blank putih/500)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>🔍 DIAGNOSA STAYCATION</h1>";
echo "Versi PHP Server: " . phpversion() . "<br><hr>";

// 2. Cek Apakah Folder Vendor (Library) Ada?
$autoloadPath = __DIR__ . '/../vendor/autoload.php';

if (!file_exists($autoloadPath)) {
    echo "<h2 style='color:red'>❌ FATAL ERROR: Folder 'vendor' TIDAK DITEMUKAN!</h2>";
    echo "<strong>Penyebab:</strong> Vercel belum menjalankan 'composer install'.<br>";
    echo "<strong>Solusi:</strong> Cek menu Settings > Build > Install Command di Vercel.";
    die(); // Matikan proses di sini
} else {
    echo "✅ Folder Vendor Ditemukan.<br>";
}

// 3. Cek Apakah File .env Terbaca?
if (!file_exists(__DIR__ . '/../.env') && getenv('APP_KEY') === false) {
    echo "<h2 style='color:orange'>⚠️ PERINGATAN: Environment Variables Tidak Terbaca</h2>";
} else {
    echo "✅ Environment Variables Aman.<br>";
}

echo "<hr><h3>Mencoba Menjalankan Laravel...</h3>";

// 4. Jalankan Laravel (Kode Asli)
define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require $autoloadPath;

$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Illuminate\Http\Request::capture());