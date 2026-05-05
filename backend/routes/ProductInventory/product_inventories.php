<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductInventory\ProductInventoryController;

/*
|--------------------------------------------------------------------------
| API Routes - Inventario de Productos/Product Inventories
|--------------------------------------------------------------------------
*/

Route::get('/', [ProductInventoryController::class, 'index']);
Route::post('/', [ProductInventoryController::class, 'store']);
Route::get('/{id}', [ProductInventoryController::class, 'show']);
Route::post('/{id}/verify', [ProductInventoryController::class, 'verify']);
Route::delete('/{id}', [ProductInventoryController::class, 'destroy']);
