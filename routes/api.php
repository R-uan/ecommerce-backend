<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ManufacturersController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ValidateAdmin;
use App\Http\Middleware\ValidateUser;
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

/**
 * Public Endpoints
 */
Route::prefix('/products')->group(function () {
    Route::controller(ProductsController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/{id}', 'show')->where('id', '[0-9]+');
        Route::get('/search', 'search');
    });
});

Route::prefix('/manufacturers')->group(function () {
    Route::controller(ManufacturersController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/{id}', 'show')->where('id', '[0-9]+');
        Route::get('/search', 'search');
    });
});

/**
 * Authentication Endpoint
 */
Route::prefix('/auth')->group(function () {
    Route::controller(AuthenticationController::class)
        ->group(function () {
            Route::get('/login', 'login');
            Route::get('/refresh', 'refresh');
        });
    Route::post('/register', [UserController::class, 'register']);
});

/**
 * Authenticated Client Endpoint
 */
Route::middleware(ValidateUser::class)->group(function () {
    /**
     * Orders
     */
    Route::controller(OrdersController::class)->group(function () {
        Route::get('/orders/order', 'create');
        Route::get('/orders', 'show');
    });
});

/**
 * Administrative Endpoints
 */
Route::middleware(ValidateAdmin::class)->group(function () {
    Route::prefix("/admin")->group(function () {
        # Products
        Route::prefix("/products")->group(function () {
            Route::controller(ProductsController::class)->group(function () {
                Route::post('/', 'store');
                Route::patch('/{id}', 'update')->where('id', '[0-9]+');
                Route::delete('/{id}', 'destroy')->where('id', '[0-9]+');
            });
        });

        # Manufacturers
        Route::prefix("/manufacturers")->group(function () {
            Route::controller(ManufacturersController::class)->group(function () {
                Route::post('/', 'store');
                Route::patch('/{id}', 'update')->where('id', '[0-9]+');
                Route::delete('/{id}', 'destroy')->where('id', '[0-9]+');
            });
        });

        # Orders
        Route::prefix('/orders')->group(function () {
            Route::controller(OrdersController::class)->group(function () {
                Route::get('/orders', 'index');
            });
        });
    });
});
