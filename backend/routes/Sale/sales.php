<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Sale\SalesController;

/*
|--------------------------------------------------------------------------
| API Routes - Sales/Ventas
|--------------------------------------------------------------------------
|
*/

Route::get('/byDepartment', [SalesController::class, 'getSalesByDepartmentGeneral']);
Route::get('/byStore/{storeId}', [SalesController::class, 'getSalesByDepartmentByStore']);
Route::get('/byStore/{storeId}/filtered', [SalesController::class, 'getSalesByDepartmentStoreWithDates']);
Route::get('/transactions/department/{departmentId}', [SalesController::class, 'getTransactionsByDepartment']);

// CRUD Ventas
Route::get('/', [SalesController::class, 'index']);
// ARREGLAR ESTA API YA QUE CUANDO EN EL FRONT SE AGREGA MAS DE UNA VENTA SALEN LAS DOS EN UNA CELDA 
Route::post('/', [SalesController::class, 'store']);
Route::get('/{id}', [SalesController::class, 'show']);
Route::put('/{id}', [SalesController::class, 'update']);
Route::delete('/{id}', [SalesController::class, 'destroy']);
