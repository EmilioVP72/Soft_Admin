<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Output\OutputController;

/*
|--------------------------------------------------------------------------
| API Routes - Salidas/Outputs
|--------------------------------------------------------------------------
*/

Route::get('/', [OutputController::class, 'index']);
Route::post('/', [OutputController::class, 'store']);
Route::get('/{id}', [OutputController::class, 'show']);
Route::put('/{id}', [OutputController::class, 'update']);
Route::delete('/{id}', [OutputController::class, 'destroy']);
