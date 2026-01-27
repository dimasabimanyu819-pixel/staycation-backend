<?php

use Illuminate\Http\Request;

// =================================================================
// 1. RAW ERROR HANDLING (Jaga-jaga kalau PHP mati total)
// =================================================================
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('LARAVEL_START', microtime(true));

// 2. Load Autoloader
if (file_exists(__DIR__.'/../vendor/autoload.php')) {
    require __DIR__.'/../vendor/autoload.php';
} else {
    die("VENDOR MISSING. Jalankan composer install.");
}

// 3. Boot Laravel
$app = require_once __DIR__.'/../bootstrap/app.php';

// =================================================================
// 4. GOD MODE CONFIG INJECTION (SUNTIK MATI)
// =================================================================
// Kita menimpa konfigurasi Laravel langsung di memori.
// Tidak peduli apa isi .env atau config/database.php, kode ini yang menang.

// A. APP KEY & DEBUG (Wajib)
$app->make('config')->set('app.key', 'base64:iKB6Mjm4Ko+geGJjqlzKMinpZqShp6PdbACHFAbBsEI=');
$app->make('config')->set('app.debug', true);
$app->make('config')->set('app.env', 'production');

// B. DATABASE CONNECTION (Data "Hijau" Mas Tadi)
$app->make('config')->set('database.default', 'mysql');
$app->make('config')->set('database.connections.mysql', [
    'driver'    => 'mysql',
    'host'      => 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com',
    'port'      => 4000,
    'database'  => 'staycation-db',
    'username'  => '4DYyn4ujWLMYNpK.root',
    'password'  => 'QQhLZbWir5XT9sPv', // Password Juara!
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix'    => '',
    'strict'    => true,
    'engine'    => null,
    'sslmode'   => 'verify-ca',
    'options'   => [
        PDO::MYSQL_ATTR_SSL_CA => '/etc/ssl/certs/ca-certificates.crt',
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    ]
]);

// C. FIX STORAGE VERCEL
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
// 5. EXECUTE
// =================================================================

try {
    $request = Request::capture();
    $response = $app->handle($request);
    $response->send();
    $app->terminate($request, $response);
} catch (\Throwable $e) {
    // Kalau masih error, tampilkan layar merah dengan detail
    echo "<div style='font-family:sans-serif; background:#a00; color:#fff; padding:20px;'>";
    echo "<h1>🔥 FATAL ERROR</h1>";
    echo "<h3>" . $e->getMessage() . "</h3>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}