<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/helper.php'; 

use App\Core\Route;

// Muat semua route
require_once __DIR__ . '/../app/routes/web.php';

// Jalankan router
Route::dispatch();