<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Department\DepartmentController;

Route::middleware('auth:api')->group(function () {
    Route::get('/all', [DepartmentController::class, 'index']);
    Route::get('/store/{storeId}', [DepartmentController::class, 'getByStore']);
    Route::get('/{id}', [DepartmentController::class, 'show']);
    Route::post('/create', [DepartmentController::class, 'store']);
    Route::put('/update/{id}', [DepartmentController::class, 'update']);
    Route::delete('/delete/{id}', [DepartmentController::class, 'destroy']);
});
