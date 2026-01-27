<?php

// =============================================================
// 1. HARDCODE ENV (SUNTIK PAKSA)
// =============================================================
// Masukkan data asli .env laptop Mas di sini.

putenv('APP_KEY=base64:iKB6Mjm4Ko+geGJjqlzKMinpZqShp6PdbACHFAbBsEI='); // <-- PASTIKAN INI BENAR
putenv('APP_DEBUG=true');
putenv('APP_ENV=production');

// DATABASE (Isi dengan benar, jangan sampai salah ketik/spasi)
putenv('DB_CONNECTION=mysql');
putenv('DB_HOST=gateway01.ap-southeast-1.prod.aws.tidbcloud.com'); 
putenv('DB_PORT=4000');
putenv('DB_DATABASE=staycation-db');
putenv('4DYyn4ujWLMYNpK.root'); // <-- ISI USERNAME
putenv('cbPEJnTClr7dxCWx'); // <-- ISI PASSWORD
putenv('DB_SSL_MODE=required');

// =============================================================
// 2. BERSIHKAN CACHE OTOMATIS
// =============================================================
// Hapus config cache setiap kali loading supaya ENV baru terbaca
$configCache = __DIR__.'/../bootstrap/cache/config.php';
if (file_exists($configCache)) {
    @unlink($configCache);
}

// =============================================================
// 3. BOOTING LARAVEL
// =============================================================
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

// =============================================================
// 4. FIX STORAGE PATH (WAJIB VERCEL)
// =============================================================
$app->useStoragePath('/tmp/storage');
if (!is_dir('/tmp/storage')) {
    mkdir('/tmp/storage', 0777, true);
    mkdir('/tmp/storage/framework/views', 0777, true);
}

// =============================================================
// 5. JEBAKAN BATMAN (TRY-CATCH BLOCK)
// =============================================================
// Ini kuncinya! Kita tangkap errornya sebelum Laravel crash.

try {
    $request = Request::capture();
    $response = $app->handle($request);
    $response->send();
    $app->terminate($request, $response);
} catch (\Throwable $e) {
    // Kalau error, cetak teks polos saja. Jangan panggil View!
    echo "<div style='font-family: monospace; background: #222; color: #fff; padding: 20px;'>";
    echo "<h1 style='color: #ff5555'>🔥 ERROR TERTANGKAP!</h1>";
    echo "<h3>Pesan Error Asli:</h3>";
    echo "<pre style='color: #ffff55; font-size: 16px;'>" . $e->getMessage() . "</pre>";
    echo "<hr>";
    echo "<h3>Lokasi:</h3>";
    echo "<p>" . $e->getFile() . " di baris " . $e->getLine() . "</p>";
    echo "<hr>";
    echo "<h3>Stack Trace (Ringkas):</h3>";
    echo "<pre>" . substr($e->getTraceAsString(), 0, 1000) . "...</pre>";
    echo "</div>";
    die(); // Matikan proses di sini
}