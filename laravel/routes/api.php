<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RouteController;
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

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:api', 'role:Admin'])->group(function () {

    Route::delete('/cars/{id}', [CarController::class, 'destroy'])->name('cars.destroy');

    Route::delete('/clients/{id}', [CarController::class, 'destroy'])->name('clients.destroy');

    Route::delete('/drivers/{id}', [CarController::class, 'destroy'])->name('drivers.destroy');

    Route::delete('/routes/{id}', [CarController::class, 'destroy'])->name('routes.destroy');

    Route::delete('/orders/{id}', [CarController::class, 'destroy'])->name('orders.destroy');
});

Route::middleware(['auth:api', 'role:Manager,Admin'])->group(function () {

    Route::post('/cars', [CarController::class, 'store'])->name('cars.store');
    Route::put('/cars/{id}', [CarController::class, 'update'])->name('cars.update');

    Route::post('/clients', [CarController::class, 'store'])->name('clients.store');
    Route::put('/clients/{id}', [CarController::class, 'update'])->name('clients.update');

    Route::post('/drivers', [CarController::class, 'store'])->name('drivers.store');
    Route::put('/drivers/{id}', [CarController::class, 'update'])->name('drivers.update');

    Route::post('/routes', [CarController::class, 'store'])->name('routes.store');
    Route::put('/routes/{id}', [CarController::class, 'update'])->name('routes.update');

    Route::post('/orders', [CarController::class, 'store'])->name('orders.store');
    Route::put('/orders/{id}', [CarController::class, 'update'])->name('orders.update');
});

Route::middleware(['auth:api', 'role:Client,Manager,Admin'])->group(function () {

    Route::get('/cars', [CarController::class, 'index'])->name('cars.index');
    Route::get('/cars/{id}', [CarController::class, 'show'])->name('cars.show');

    Route::get('/clients', [CarController::class, 'index'])->name('clients.index');
    Route::get('/clients/{id}', [CarController::class, 'show'])->name('clients.show');

    Route::get('/drivers', [CarController::class, 'index'])->name('drivers.index');
    Route::get('/drivers/{id}', [CarController::class, 'show'])->name('drivers.show');

    Route::get('/routes', [CarController::class, 'index'])->name('routes.index');
    Route::get('/routes/{id}', [CarController::class, 'show'])->name('routes.show');

    Route::get('/orders', [CarController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [CarController::class, 'show'])->name('orders.show');
});
