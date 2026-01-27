<?php

// ====================================================================
// 1. CACHE BUSTER (WAJIB ADA LAGI)
// ====================================================================
// Kita paksa Laravel mencari cache di folder /tmp (yang pasti kosong).
// Tanpa ini, Vercel akan membaca settingan lama yang error.
$tmpPath = '/tmp/laravel_cache';
if (!is_dir($tmpPath)) mkdir($tmpPath, 0777, true);

putenv("APP_CONFIG_CACHE={$tmpPath}/config.php");
putenv("APP_SERVICES_CACHE={$tmpPath}/services.php");
putenv("APP_PACKAGES_CACHE={$tmpPath}/packages.php");
putenv("APP_ROUTES_CACHE={$tmpPath}/routes.php");

// ====================================================================
// 2. INJECT CREDENTIALS (YANG SUDAH BENAR)
// ====================================================================

// APP KEY (Pastikan sama dengan laptop)
putenv('APP_KEY=base64:iKB6Mjm4Ko+geGJjqlzKMinpZqShp6PdbACHFAbBsEI=');
putenv('APP_DEBUG=true');
putenv('APP_ENV=production');

// DATABASE (Sesuai yang Mas buat tadi)
putenv('DB_CONNECTION=mysql');
putenv('DB_HOST=gateway01.ap-southeast-1.prod.aws.tidbcloud.com');
putenv('DB_PORT=4000');
putenv('DB_DATABASE=staycation-db');   // <-- SUDAH SAYA GANTI JADI staycation-db
putenv('DB_USERNAME=4DYyn4ujWLMYNpK.root'); 
putenv('DB_PASSWORD=QQhLZbWir5XT9sPv'); // <-- Password HurufAngka
putenv('DB_SSL_MODE=required');

// ====================================================================
// 3. BOOTING
// ====================================================================
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Load Maintenance
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Load Composer
require __DIR__.'/../vendor/autoload.php';

// Load App
$app = require_once __DIR__.'/../bootstrap/app.php';

// ====================================================================
// 4. FIX STORAGE PATH
// ====================================================================
$app->useStoragePath('/tmp/storage');
if (!is_dir('/tmp/storage')) {
    mkdir('/tmp/storage', 0777, true);
    mkdir('/tmp/storage/framework/views', 0777, true);
}

// ====================================================================
// 5. EKSEKUSI DENGAN JEBAKAN ERROR (TRY-CATCH)
// ====================================================================
// Kita tangkap errornya supaya tidak muncul "View not found" lagi.

try {
    $request = Request::capture();
    $response = $app->handle($request);
    $response->send();
    $app->terminate($request, $response);
} catch (\Throwable $e) {
    // Kalau error, munculkan pesan aslinya!
    echo "<div style='background:#222; color:#fff; padding:20px; font-family:monospace;'>";
    echo "<h1 style='color:red'>🔥 Error Lagi? Mari Kita Lihat:</h1>";
    echo "<h3>Pesan Error:</h3>";
    echo "<pre style='color:yellow; font-size:16px;'>" . $e->getMessage() . "</pre>";
    echo "<hr>";
    echo "<h3>Lokasi:</h3>";
    echo "<p>" . $e->getFile() . " baris " . $e->getLine() . "</p>";
    echo "</div>";
    die();
}