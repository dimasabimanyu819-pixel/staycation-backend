<?php

// ====================================================================
// 1. DATA KONEKSI (YANG SUDAH TERBUKTI HIJAU)
// ====================================================================
// Kita tanam credential ini supaya Laravel tidak punya pilihan lain selain SUKSES.

putenv('APP_KEY=base64:iKB6Mjm4Ko+geGJjqlzKMinpZqShp6PdbACHFAbBsEI=');
putenv('APP_DEBUG=true');
putenv('APP_ENV=production');

putenv('DB_CONNECTION=mysql');
putenv('DB_HOST=gateway01.ap-southeast-1.prod.aws.tidbcloud.com');
putenv('DB_PORT=4000');
putenv('DB_DATABASE=staycation-db');
putenv('DB_USERNAME=4DYyn4ujWLMYNpK.root');
putenv('DB_PASSWORD=QQhLZbWir5XT9sPv'); // <-- Password Juara Kita!
putenv('DB_SSL_MODE=required');

// Simpan ke $_ENV juga (Cadangan)
$_ENV['DB_HOST'] = getenv('DB_HOST');
$_ENV['DB_USERNAME'] = getenv('DB_USERNAME');
$_ENV['DB_PASSWORD'] = getenv('DB_PASSWORD');

// ====================================================================
// 2. OBAT RACUN CACHE (CACHE BUSTER) - WAJIB
// ====================================================================
// Ini bagian terpenting! Kita buang cache lama ke folder kosong (/tmp)
// Supaya Laravel membaca ulang credential di atas.

$tmpPath = '/tmp/laravel_cache_v2'; // Ganti nama folder biar fresh
if (!is_dir($tmpPath)) mkdir($tmpPath, 0777, true);

putenv("APP_CONFIG_CACHE={$tmpPath}/config.php");
putenv("APP_SERVICES_CACHE={$tmpPath}/services.php");
putenv("APP_PACKAGES_CACHE={$tmpPath}/packages.php");
putenv("APP_ROUTES_CACHE={$tmpPath}/routes.php");

// ====================================================================
// 3. BOOTING LARAVEL
// ====================================================================

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Cek Maintenance
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Load Composer Autoload (Bensinnya)
if (file_exists(__DIR__.'/../vendor/autoload.php')) {
    require __DIR__.'/../vendor/autoload.php';
} else {
    // Jaga-jaga kalau composer gagal
    die("<h1>Error: Folder Vendor Hilang</h1><p>Silakan Redeploy Vercel.</p>");
}

// Load Aplikasi Laravel (Mesinnya)
$app = require_once __DIR__.'/../bootstrap/app.php';

// ====================================================================
// 4. FIX VERCEL STORAGE (READ-ONLY FIX)
// ====================================================================
// Pindahkan penyimpanan file sementara ke /tmp
$app->useStoragePath('/tmp/storage');

if (!is_dir('/tmp/storage')) {
    mkdir('/tmp/storage', 0777, true);
    mkdir('/tmp/storage/framework/views', 0777, true);
    mkdir('/tmp/storage/framework/cache', 0777, true);
    mkdir('/tmp/storage/framework/sessions', 0777, true);
    mkdir('/tmp/storage/logs', 0777, true);
}

// ====================================================================
// 5. GASPOL! (JALANKAN)
// ====================================================================

// Kita pakai Try-Catch jaga-jaga kalau ada error kecil lainnya
try {
    $request = Request::capture();
    $response = $app->handle($request);
    $response->send();
    $app->terminate($request, $response);
} catch (\Throwable $e) {
    // Kalau masih error, tampilkan detailnya (bukan 500 blank)
    echo "<div style='font-family:monospace; padding:20px; background:#f00; color:#fff;'>";
    echo "<h1>🔥 LARAVEL ERROR</h1>";
    echo "<h3>" . $e->getMessage() . "</h3>";
    echo "<p>File: " . $e->getFile() . " baris " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}