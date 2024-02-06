<?php

use App\Http\Controllers\ManufacturersController;
use App\Http\Controllers\ProductsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
 */

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('/products')->group(function () {
    Route::controller(ProductsController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/search', 'search');
        Route::get('/{id}', 'show')->where('id', '[0-9]+');
        Route::post('/', 'store');
        Route::delete('/{id}', 'destroy')->where('id', '[0-9]+');
    });
});

/* Route::get('/', [ProductsController::class, 'index']);
Route::get('/{id}', [ProductsController::class, 'show']); */
Route::get('/manufacturers', [ManufacturersController::class, 'index']);