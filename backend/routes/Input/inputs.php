<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Input\InputController;

/*
|--------------------------------------------------------------------------
| API Routes - Entradas/Inputs
|--------------------------------------------------------------------------
*/

Route::get('/', [InputController::class, 'index']);
Route::post('/', [InputController::class, 'store']);
Route::get('/{id}', [InputController::class, 'show']);
Route::put('/{id}', [InputController::class, 'update']);
Route::delete('/{id}', [InputController::class, 'destroy']);
