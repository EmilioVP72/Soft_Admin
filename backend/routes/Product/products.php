<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\ProductInventory\ProductInventoryController;

/*
|--------------------------------------------------------------------------
| API Routes - Productos/Products
|--------------------------------------------------------------------------
*/

Route::get('/', [ProductController::class, 'index']);
Route::post('/', [ProductController::class, 'store']);
Route::get('/{id}', [ProductController::class, 'show']);
Route::put('/{id}', [ProductController::class, 'update']);
Route::delete('/{id}', [ProductController::class, 'destroy']);
Route::get('/{id}/inventories', [ProductInventoryController::class, 'getByProduct']);
