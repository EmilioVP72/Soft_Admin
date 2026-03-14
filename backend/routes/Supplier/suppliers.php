<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Supplier\SupplierController;

/*
|--------------------------------------------------------------------------
| API Routes - Proveedores/Suppliers
|--------------------------------------------------------------------------
*/

Route::get('/', [SupplierController::class, 'index']);
Route::post('/', [SupplierController::class, 'store']);
Route::get('/payments/all', [SupplierController::class, 'getAllPayments']);
Route::get('/{id}', [SupplierController::class, 'show']);
Route::put('/{id}', [SupplierController::class, 'update']);
Route::delete('/{id}', [SupplierController::class, 'destroy']);
Route::post('/{id}/payments', [SupplierController::class, 'storePayment']);
Route::get('/{id}/payments', [SupplierController::class, 'getSupplierPayments']);
