<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    
    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
        Route::get('/me', [AuthController::class, 'me'])->name('me');
    });
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');


Route::middleware('auth:api')->group(function () {
    // Rutas de Ventas/Sales
    Route::prefix('sales')->group(function () {
        require __DIR__ . '/Sale/sales.php';
    });

    // Rutas de Tiendas/Store
    Route::prefix('stores')->group(function () {
        require __DIR__ . '/Store/stores.php';
    });

    // Rutas de Empleados/Employees
    Route::prefix('employees')->group(function () {
        require __DIR__ . '/Employee/employees.php';
    });

    // Rutas de Localidades/Localities
    Route::prefix('localities')->group(function () {
        require __DIR__ . '/Locality/localities.php';
    });
});