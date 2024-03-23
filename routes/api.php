<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ManufacturersController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\PlanetDestinationController;
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

#region Public Endpoints

Route::prefix('/products')->group(function () {
  Route::controller(ProductsController::class)->group(function () {
    Route::get('/', 'All'); # Has tests
    Route::get('/search', 'Search'); # Has tests
    Route::get('/miniatures', 'AllMiniatures'); # Has tests
    Route::post('/miniatures', 'SomeMiniatures'); # Has tests
    Route::get('/{id}', 'One')->where('id', '[0-9]+'); # Has tests

    Route::middleware(ValidateAdmin::class)->group(function () {
      Route::post('/', 'Create'); #Has tests
      Route::patch('/{id}', 'Update')->where('id', '[0-9]+'); # Has tests
      Route::delete('/{id}', 'Destroy')->where('id', '[0-9]+'); # Has tests
    });
  });

});

Route::prefix('/manufacturers')->group(function () {
  Route::controller(ManufacturersController::class)->group(function () {
    Route::get('/', 'All');
    Route::get('/search', 'Search');
    Route::get('/{name}/search', 'Products');
    Route::get('/{id}', 'One')->where('id', '[0-9]+');

    Route::middleware(ValidateAdmin::class)->group(function () {
      Route::post('/', 'Create');
      Route::patch('/{id}', 'Update')->where('id', '[0-9]+');
      Route::delete('/{id}', 'Destroy')->where('id', '[0-9]+');
    });
  });
});

Route::prefix('/auth')->group(function () {
  Route::controller(AuthenticationController::class)
    ->group(function () {
      Route::post('/login', 'Login');
      Route::get('/refresh', 'Refresh');
    });

  Route::post('/register', [UserController::class, 'Register']);
});

Route::prefix('/destinations')->group(function () {
  Route::controller(PlanetDestinationController::class)->group(function () {
    Route::get('/{name}', 'One');
    Route::get('/', 'All');
    Route::delete('/{id}', 'Delete');
    Route::patch('/{id}', 'Update');
  });
});

#endregion

#region User Endpoints

Route::middleware(ValidateUser::class)->group(function () {
  Route::controller(OrdersController::class)->group(function () {
    Route::post('/orders', 'Create');
    Route::get('/orders', 'ClientOrders');
    Route::get('/orders/{id}', 'One')->where('id', '[0-9]+');
  });
});

#endregion

#region Administrative Endpoints

Route::controller(ManufacturersController::class)->group(function () {
  Route::controller(OrdersController::class)->group(function () {
    Route::prefix('/orders')->group(function () {
      Route::get('/orders', 'All');
      Route::get('/search', 'Search');
    });
  });
});

#endregion