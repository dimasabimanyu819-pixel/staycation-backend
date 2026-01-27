<?php

use Illuminate\Http\Request;

// =================================================================
// 1. INJEKSI DATA "LAYAR HIJAU" (ENV FORCING)
// =================================================================
// Kita masukkan data yang TADI SUDAH TERBUKTI SUKSES ke semua celah memori PHP.
// (putenv, $_ENV, dan $_SERVER) supaya Laravel PASTI membacanya.

$variables = [
    'APP_KEY'       => 'base64:iKB6Mjm4Ko+geGJjqlzKMinpZqShp6PdbACHFAbBsEI=',
    'APP_DEBUG'     => 'true',
    'APP_ENV'       => 'production',
    
    'DB_CONNECTION' => 'mysql',
    'DB_HOST'       => 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com',
    'DB_PORT'       => '4000',
    'DB_DATABASE'   => 'staycation-db',
    'DB_USERNAME'   => '4DYyn4ujWLMYNpK.root',
    'DB_PASSWORD'   => 'QQhLZbWir5XT9sPv', // <--- PASSWORD YANG TADI SUKSES (HIJAU)
    'DB_SSL_MODE'   => 'required',
];

foreach ($variables as $key => $value) {
    putenv("$key=$value");
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}

// =================================================================
// 2. CACHE BUSTER (PEMBERSIH MEMORI)
// =================================================================
// Kita paksa Laravel membaca ulang config dari data di atas.
// Kita arahkan cache ke file kosong di /tmp.

$tmpCache = '/tmp/config.php';
putenv("APP_CONFIG_CACHE=$tmpCache");
if (file_exists($tmpCache)) {
    @unlink($tmpCache); // Hapus cache lama jika ada
}

// =================================================================
// 3. BOOTING LARAVEL STANDARD
// =================================================================

define('LARAVEL_START', microtime(true));

// Load Maintenance
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Load Autoload
if (file_exists(__DIR__.'/../vendor/autoload.php')) {
    require __DIR__.'/../vendor/autoload.php';
} else {
    die("Vendor folder missing. Composer install failed.");
}

// Load App
$app = require_once __DIR__.'/../bootstrap/app.php';

// =================================================================
// 4. FIX STORAGE PATH (WAJIB VERCEL)
// =================================================================
$app->useStoragePath('/tmp/storage');
$dirs = [
    '/tmp/storage', 
    '/tmp/storage/framework/views', 
    '/tmp/storage/framework/sessions', 
    '/tmp/storage/logs'
];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) mkdir($dir, 0777, true);
}

// =================================================================
// 5. JALANKAN REQUEST
// =================================================================

// Kita pakai Try-Catch sederhana hanya untuk log, bukan untuk diagnosa
try {
    $request = Request::capture();
    $response = $app->handle($request);
    $response->send();
    $app->terminate($request, $response);
} catch (\Throwable $e) {
    echo "<div style='font-family:sans-serif; padding:20px; background:#f00; color:#fff;'>";
    echo "<h1>💥 Error Terjadi</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . " baris " . $e->getLine() . "</p>";
    echo "</div>";
}