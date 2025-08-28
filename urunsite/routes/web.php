<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Test route
Route::get('/test', function () {
    return 'Laravel Web Route Çalışıyor!';
});

// API durumu için basit bir route
Route::get('/api-status', function () {
    return response()->json([
        'message' => 'Laravel API hazır!',
        'timestamp' => now()->format('Y-m-d H:i:s'),
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version()
    ]);
});
