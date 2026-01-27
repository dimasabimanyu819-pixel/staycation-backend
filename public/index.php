<?php

// ====================================================================
// 1. TES KONEKSI DATABASE LANGSUNG (RAW TEST)
// ====================================================================
// Kita cek dulu koneksinya SEBELUM Laravel loading.
// Kalau ini gagal, berarti PASSWORD TiDB 100% SALAH.

$testHost = 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com'; // <-- CEK HOST
$testUser = '4DYyn4ujWLMYNpK.root'; // <-- CEK USER
$testPass = 'QQhLZbWir5XT9sPv';   // <-- GANTI PASSWORD BARU DISINI (Tanpa Simbol)
$testDb   = 'staycation-db';
$testPort = 4000;

try {
    $dsn = "mysql:host=$testHost;port=$testPort;dbname=$testDb;sslmode=verify-ca;sslrootcert=/etc/ssl/cert.pem";
    // TiDB butuh opsi SSL khusus
    $options = [
        PDO::MYSQL_ATTR_SSL_CA => '/etc/ssl/cert.pem',
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    ];
    
    // Coba connect manual
    $pdo = new PDO($dsn, $testUser, $testPass, $options);
    
    // Kalau berhasil, diam saja (lanjut ke bawah).
} catch (PDOException $e) {
    // Kalau gagal, langsung teriak errornya disini!
    echo "<div style='font-family:sans-serif; padding:20px; background:#ffebeb; border:2px solid red;'>";
    echo "<h1 style='color:red'>🛑 STOP! Password Database Salah.</h1>";
    echo "<p>Laravel belum dijalankan karena login database saja sudah gagal.</p>";
    echo "<h3>Pesan Dari TiDB:</h3>";
    echo "<code style='background:#fff; padding:5px;'>" . $e->getMessage() . "</code>";
    echo "<br><br><strong>Solusi:</strong> Reset password TiDB lagi, gunakan huruf & angka saja.";
    echo "</div>";
    die(); // Matikan proses
}

// ====================================================================
// 2. CONFIG LARAVEL (Hanya jalan kalau Tes Koneksi di atas SUKSES)
// ====================================================================

// Cache Buster
$tmpPath = '/tmp/laravel_cache';
if (!is_dir($tmpPath)) mkdir($tmpPath, 0777, true);
putenv("APP_CONFIG_CACHE={$tmpPath}/config.php");
putenv("APP_SERVICES_CACHE={$tmpPath}/services.php");
putenv("APP_PACKAGES_CACHE={$tmpPath}/packages.php");

// Inject Config Laravel
putenv('APP_KEY=base64:iKB6Mjm4Ko+geGJjqlzKMinpZqShp6PdbACHFAbBsEI='); 
putenv('APP_DEBUG=true');
putenv('APP_ENV=production');

putenv('DB_CONNECTION=mysql');
putenv("DB_HOST=$testHost");
putenv("DB_PORT=$testPort");
putenv("DB_DATABASE=$testDb");
putenv("DB_USERNAME=$testUser");
putenv("DB_PASSWORD=$testPass");
putenv('DB_SSL_MODE=required');

// Fix Storage
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->useStoragePath('/tmp/storage');
if (!is_dir('/tmp/storage')) {
    mkdir('/tmp/storage', 0777, true);
    mkdir('/tmp/storage/framework/views', 0777, true);
}

// Jalankan Laravel
use Illuminate\Http\Request;
try {
    $request = Request::capture();
    $response = $app->handle($request);
    $response->send();
    $app->terminate($request, $response);
} catch (\Throwable $e) {
    echo "<h1>Error Laravel:</h1>" . $e->getMessage();
}