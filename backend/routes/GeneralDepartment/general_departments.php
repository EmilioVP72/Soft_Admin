<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeneralDepartment\GeneralDepartmentController;

Route::middleware('auth:api')->group(function () {
    Route::get('/all', [GeneralDepartmentController::class, 'index']);
    Route::get('/store/{storeId}', [GeneralDepartmentController::class, 'getByStore']);
    Route::get('/{id}', [GeneralDepartmentController::class, 'show']);
    Route::post('/create', [GeneralDepartmentController::class, 'store']);
    Route::put('/update/{id}', [GeneralDepartmentController::class, 'update']);
    Route::delete('/delete/{id}', [GeneralDepartmentController::class, 'destroy']);
});
