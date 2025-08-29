<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductFilterController;
use App\Http\Controllers\RedisController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Product Routes - Temel CRUD işlemleri
Route::prefix('products')->group(function () {

    Route::get('/', [ProductController::class, 'index']);                    // GET /api/products
    Route::get('/{id}', [ProductController::class, 'show']);                // GET /api/products/{id}
    Route::post('/', [ProductController::class, 'store']);                  // POST /api/products (Queue ile)
    Route::put('/{id}', [ProductController::class, 'update']);              // PUT /api/products/{id} (Queue ile)
    Route::delete('/{id}', [ProductController::class, 'destroy']);          // DELETE /api/products/{id} (Queue ile)

    // Filters Operations - Ayrı controller kullanıyoruz
    Route::prefix('filters')->group(function () {
        Route::get('/options', [ProductFilterController::class, 'filterOptions']);    // GET /api/products/filters/options
        Route::get('/clear', [ProductFilterController::class, 'clearFilters']);       // GET /api/products/filters/clear
        Route::get('/active', [ProductFilterController::class, 'activeFilters']);     // GET /api/products/filters/active
    });
});

// Redis Routes
Route::prefix('redis')->group(function () {
    Route::get('/', [RedisController::class, 'index']);                     // GET /api/redis
    Route::get('/{id}', [RedisController::class, 'show']);                 // GET /api/redis/{id}
    Route::post('/', [RedisController::class, 'store']);                   // POST /api/redis
    Route::put('/{id}', [RedisController::class, 'update']);               // PUT /api/redis/{id}
    Route::delete('/{id}', [RedisController::class, 'destroy']);           // DELETE /api/redis/{id}
});
