<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// =================================================================
// 1. SUNTIK CONFIGURASI (HARDCODE) - WAJIB UNTUK VERCEL
// =================================================================
// Karena DB sudah connect, kita masukkan credential yang sudah terbukti benar.

// APP KEY (Pastikan sama dengan yang di laptop)
putenv('APP_KEY=base64:iKB6Mjm4Ko+geGJjqlzKMinpZqShp6PdbACHFAbBsEI=');
putenv('APP_DEBUG=true');
putenv('APP_ENV=production');

// DATABASE CREDENTIALS (YANG SUDAH SUKSES TADI)
putenv('DB_CONNECTION=mysql');
putenv('DB_HOST=gateway01.ap-southeast-1.prod.aws.tidbcloud.com');
putenv('DB_PORT=4000');
// Nama database diganti jadi staycation-db (karena sudah dibuat)
putenv('DB_DATABASE=staycation-db'); 
putenv('DB_USERNAME=4DYyn4ujWLMYNpK.root'); 
putenv('DB_PASSWORD=QQhLZbWir5XT9sPv'); // Password tanpa simbol
putenv('DB_SSL_MODE=required');

// Simpan ke $_ENV juga agar terbaca oleh library lain
$_ENV['APP_KEY']     = getenv('APP_KEY');
$_ENV['DB_HOST']     = getenv('DB_HOST');
$_ENV['DB_DATABASE'] = getenv('DB_DATABASE');
$_ENV['DB_USERNAME'] = getenv('DB_USERNAME');
$_ENV['DB_PASSWORD'] = getenv('DB_PASSWORD');

// =================================================================
// 2. LOADING LIBRARY LARAVEL (AUTOLOAD)
// =================================================================

// Cek Maintenance Mode
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// INI YANG TADI ERROR. KITA PASTIKAN DILOAD DULUAN.
if (file_exists(__DIR__.'/../vendor/autoload.php')) {
    require __DIR__.'/../vendor/autoload.php';
} else {
    die("❌ Error Fatal: Folder '/vendor' tidak ditemukan. Jalankan 'composer install' di Vercel.");
}

// =================================================================
// 3. BOOTING APLIKASI
// =================================================================

$app = require_once __DIR__.'/../bootstrap/app.php';

// =================================================================
// 4. FIX STORAGE PATH (KHUSUS VERCEL)
// =================================================================
// Pindahkan penyimpanan cache/view ke folder /tmp (karena Vercel read-only)

$app->useStoragePath('/tmp/storage');

if (!is_dir('/tmp/storage')) {
    mkdir('/tmp/storage', 0777, true);
    mkdir('/tmp/storage/framework/views', 0777, true);
    mkdir('/tmp/storage/framework/cache', 0777, true);
    mkdir('/tmp/storage/framework/sessions', 0777, true);
}

// =================================================================
// 5. JALANKAN REQUEST
// =================================================================

$app->handleRequest(Request::capture());