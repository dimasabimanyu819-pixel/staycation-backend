<?php

// =================================================================
// 1. CONFIGURATION INJECTION (SUNTIKAN KHUSUS VERCEL)
// =================================================================

// Kita paksa aplikasi mencatat Environment Variable sebelum Laravel nyala
// agar tidak ada drama "Variable not found" atau "View not found".

// APP_KEY Mas (Sudah saya masukkan sesuai yang dikirim tadi)
putenv('APP_KEY=base64:iKB6Mjm4Ko+geGJjqlzKMinpZqShp6PdbACHFAbBsEI=');
$_ENV['APP_KEY'] = getenv('APP_KEY');

// --- BAGIAN INI WAJIB MAS ISI DENGAN DATA DATABASE ---
putenv('DB_CONNECTION=mysql');
putenv('gateway01.ap-southeast-1.prod.aws.tidbcloud.com'); // <-- GANTI INI
putenv('DB_PORT=4000');
putenv('DB_DATABASE=staycation-db');
putenv('4DYyn4ujWLMYNpK.root'); // <-- ISI USER TIDB
putenv('cbPEJnTClr7dxCWx'); // <-- ISI PASSWORD TIDB
putenv('DB_SSL_MODE=required');

// Settingan Tambahan
putenv('APP_DEBUG=true');
putenv('APP_ENV=production');
putenv('LOG_CHANNEL=stderr');

// Masukkan ke $_ENV juga supaya terbaca library lain
$_ENV['DB_CONNECTION'] = 'mysql';
$_ENV['DB_HOST']       = getenv('DB_HOST');
$_ENV['DB_PORT']       = '4000';
$_ENV['DB_DATABASE']   = 'staycation-db';
$_ENV['DB_USERNAME']   = getenv('DB_USERNAME');
$_ENV['DB_PASSWORD']   = getenv('DB_PASSWORD');
$_ENV['DB_SSL_MODE']   = 'required';


// =================================================================
// 2. BOOTING LARAVEL (STANDARD)
// =================================================================

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Hapus cache config lama jika ada (Pembersih Otomatis)
$configCache = __DIR__.'/../bootstrap/cache/config.php';
if (file_exists($configCache)) {
    @unlink($configCache);
}

// Cek Maintenance Mode
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Load Autoload
require __DIR__.'/../vendor/autoload.php';

// Booting Aplikasi
$app = require_once __DIR__.'/../bootstrap/app.php';


// =================================================================
// 3. FIX STORAGE PATH (WAJIB UNTUK VERCEL)
// =================================================================
// Vercel Read-Only, jadi kita pindahkan storage ke folder sementara (/tmp)

$app->useStoragePath('/tmp/storage');

// Buat folder storage manual jika belum ada
if (!is_dir('/tmp/storage')) {
    mkdir('/tmp/storage', 0777, true);
    mkdir('/tmp/storage/framework/views', 0777, true);
    mkdir('/tmp/storage/framework/cache', 0777, true);
    mkdir('/tmp/storage/framework/sessions', 0777, true);
}

// =================================================================
// 4. JALANKAN APLIKASI
// =================================================================

$app->handleRequest(Request::capture());