<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeneralDepartment\GeneralDepartmentController;

Route::apiResource('/', GeneralDepartmentController::class)->parameters([
    '' => 'general_department'
]);
