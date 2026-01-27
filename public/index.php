<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. Maintenance Mode
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// 2. Autoload
require __DIR__.'/../vendor/autoload.php';

// 3. Boot App
$app = require_once __DIR__.'/../bootstrap/app.php';

// --- BAGIAN PENTING VERCEL (STORAGE PATH) ---
// Kita tetap butuh ini karena Vercel Read-Only
$app->useStoragePath('/tmp/storage');
if (!is_dir('/tmp/storage')) {
    mkdir('/tmp/storage', 0777, true);
    mkdir('/tmp/storage/framework/views', 0777, true);
}
// -------------------------------------------

// 4. Run
$app->handleRequest(Request::capture());