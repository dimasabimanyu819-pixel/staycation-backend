<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. HAPUS CACHE BANDEL (Supaya konfigurasi di atas terbaca)
// Kita arahkan cache ke tempat sampah (/tmp)
$tmp = '/tmp/cache_sampah';
if (!is_dir($tmp)) mkdir($tmp, 0777, true);
putenv("APP_CONFIG_CACHE={$tmp}/config.php");
putenv("APP_SERVICES_CACHE={$tmp}/services.php");
putenv("APP_PACKAGES_CACHE={$tmp}/packages.php");

// 2. APP KEY (WAJIB ADA)
putenv('APP_KEY=base64:iKB6Mjm4Ko+geGJjqlzKMinpZqShp6PdbACHFAbBsEI=');
putenv('APP_DEBUG=true');

// 3. LOAD SYSTEM
if (file_exists(__DIR__.'/../vendor/autoload.php')) {
    require __DIR__.'/../vendor/autoload.php';
}

$app = require_once __DIR__.'/../bootstrap/app.php';

// 4. FIX VERCEL STORAGE
$app->useStoragePath('/tmp/storage');
if (!is_dir('/tmp/storage')) {
    mkdir('/tmp/storage', 0777, true);
    mkdir('/tmp/storage/framework/views', 0777, true);
}

// 5. JALAN!
$app->handleRequest(Request::capture());