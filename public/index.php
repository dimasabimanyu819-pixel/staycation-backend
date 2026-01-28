<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// =================================================================
// 1. CONFIGURASI PRODUCTION (HARDCODE)
// =================================================================

// --- A. DATABASE CONNECTION (WAJIB ADA LAGI) ---
// Kita masukkan lagi credential supaya Admin Panel bisa connect ke TiDB
putenv('DB_CONNECTION=mysql');
putenv('DB_HOST=gateway01.ap-southeast-1.prod.aws.tidbcloud.com');
putenv('DB_PORT=4000');
putenv('DB_DATABASE=staycation-db');
putenv('DB_USERNAME=4DYyn4ujWLMYNpK.root');
putenv('DB_PASSWORD=QQhLZbWir5XT9sPv'); 
putenv('DB_SSL_MODE=required');

// --- B. FIX CSS & HTTPS (PENTING BUAT ADMIN PANEL) ---
// Pastikan URL ini sesuai dengan link Vercel Anda yang muncul di browser
$appUrl = 'https://staycation-backend-demo.vercel.app'; // <-- Cek URL Mas!
putenv("APP_URL={$appUrl}");
putenv("ASSET_URL={$appUrl}");
putenv('APP_ENV=production');
putenv('APP_DEBUG=true'); 
putenv('APP_KEY=base64:iKB6Mjm4Ko+geGJjqlzKMinpZqShp6PdbACHFAbBsEI=');

// --- C. SESSION DRIVER ---
// Kita pakai 'cookie' saja untuk Vercel. Lebih ringan & aman dari error koneksi.
putenv('SESSION_DRIVER=cookie');
putenv('SESSION_SECURE_COOKIE=true');

// =================================================================
// 2. CACHE HANDLING (Supaya Config Terbaca)
// =================================================================
$tmp = '/tmp/cache_final_v1';
if (!is_dir($tmp)) mkdir($tmp, 0777, true);
putenv("APP_CONFIG_CACHE={$tmp}/config.php");
putenv("APP_SERVICES_CACHE={$tmp}/services.php");
putenv("APP_PACKAGES_CACHE={$tmp}/packages.php");

// =================================================================
// 3. LOAD SYSTEM
// =================================================================
if (file_exists(__DIR__.'/../vendor/autoload.php')) {
    require __DIR__.'/../vendor/autoload.php';
}

$app = require_once __DIR__.'/../bootstrap/app.php';

// =================================================================
// 4. FIX VERCEL STORAGE (READ-ONLY FIX)
// =================================================================
$app->useStoragePath('/tmp/storage');
if (!is_dir('/tmp/storage')) {
    mkdir('/tmp/storage', 0777, true);
    mkdir('/tmp/storage/framework/views', 0777, true);
    mkdir('/tmp/storage/framework/sessions', 0777, true);
}

// =================================================================
// 5. JALAN!
// =================================================================
$app->handleRequest(Request::capture());