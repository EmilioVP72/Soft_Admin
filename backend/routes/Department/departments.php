<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Department\DepartmentController;

Route::apiResource('/', DepartmentController::class)->parameters([
    '' => 'department'
]);
