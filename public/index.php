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

// --- TAMBAHAN KHUSUS VERCEL (SUPAYA TIDAK ERROR VIEW/LOG) ---
// Pindahkan path storage ke folder /tmp (karena Vercel read-only)
$app->useStoragePath('/tmp/storage');

// Buat folder storage di /tmp jika belum ada
if (!is_dir('/tmp/storage')) {
    mkdir('/tmp/storage', 0777, true);
    mkdir('/tmp/storage/framework/views', 0777, true);
    mkdir('/tmp/storage/framework/cache', 0777, true);
    mkdir('/tmp/storage/framework/sessions', 0777, true);
}
// -----------------------------------------------------------

$app->handleRequest(Request::capture());