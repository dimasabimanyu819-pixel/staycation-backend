<?php

// ====================================================================
// 1. CACHE BUSTER (SOLUSI INTI)
// ====================================================================
// Kita paksa Laravel mencari cache di folder /tmp (yang pasti kosong).
// Ini akan membuat Laravel MENGABAIKAN file cache lama yang rusak.
$tmpPath = '/tmp/laravel_cache';
if (!is_dir($tmpPath)) mkdir($tmpPath, 0777, true);

putenv("APP_CONFIG_CACHE={$tmpPath}/config.php");
putenv("APP_SERVICES_CACHE={$tmpPath}/services.php");
putenv("APP_PACKAGES_CACHE={$tmpPath}/packages.php");
putenv("APP_ROUTES_CACHE={$tmpPath}/routes.php");

// ====================================================================
// 2. HARDCODE ENV (SUNTIKAN)
// ====================================================================
// Masukkan data asli .env laptop Mas di sini.

// ---> JANGAN LUPA CEK APP_KEY INI <---
putenv('APP_KEY=base64:iKB6Mjm4Ko+geGJjqlzKMinpZqShp6PdbACHFAbBsEI='); 

putenv('APP_DEBUG=true');
putenv('APP_ENV=production');

// DATABASE (Isi dengan benar)
putenv('DB_CONNECTION=mysql');
putenv('DB_HOST=gateway01.ap-southeast-1.prod.aws.tidbcloud.com'); 
putenv('DB_PORT=4000');
putenv('DB_DATABASE=staycation-db');
putenv('4DYyn4ujWLMYNpK.root'); // <-- ISI USERNAME
putenv('cbPEJnTClr7dxCWx'); // <-- ISI PASSWORD
putenv('DB_SSL_MODE=required');

// ====================================================================
// 3. BOOTING LARAVEL
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
// 5. EKSEKUSI DENGAN DIAGNOSA
// ====================================================================
try {
    $request = Request::capture();
    $response = $app->handle($request);
    $response->send();
    $app->terminate($request, $response);
} catch (\Throwable $e) {
    // Kalau masih error, kita bedah lagi
    echo "<div style='font-family: monospace; background: #111; color: #f0f0f0; padding: 20px;'>";
    echo "<h1 style='color: #ff4444'>🔥 MASIH ERROR?</h1>";
    echo "<p>Tapi tenang, cache lama sudah di-bypass. Ini error aslinya:</p>";
    echo "<div style='background: #333; padding: 15px; border-left: 5px solid #ff4444;'>";
    echo "<strong>Pesan:</strong> " . $e->getMessage() . "<br>";
    echo "<strong>File:</strong> " . $e->getFile() . " baris " . $e->getLine();
    echo "</div>";
    
    // Cek apakah view provider ada di memori?
    echo "<hr><h3>Status Provider:</h3>";
    $loadedProviders = array_keys($app->getLoadedProviders());
    $viewProvider = 'Illuminate\View\ViewServiceProvider';
    if (in_array($viewProvider, $loadedProviders)) {
        echo "<span style='color:green'>✅ ViewServiceProvider TERLOAD (Aneh kalau error View).</span>";
    } else {
        echo "<span style='color:orange'>⚠️ ViewServiceProvider TIDAK TERLOAD. (Masalah Config).</span>";
    }
    echo "</div>";
    die();
}