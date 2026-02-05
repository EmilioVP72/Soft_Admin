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
