<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Cek apakah mode maintenance aktif
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Load Composer Autoload (Bensinnya Laravel)
require __DIR__.'/../vendor/autoload.php';

// Booting Aplikasi Laravel
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());