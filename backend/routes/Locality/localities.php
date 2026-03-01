<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Locality\LocalityController;

/*
|--------------------------------------------------------------------------
| API Routes Locality
|--------------------------------------------------------------------------
|*/

Route::get('/all', [LocalityController::class, 'index']);
Route::get('/OneLocality/{id}', [LocalityController::class, 'show']);
Route::post('/StoreLocality', [LocalityController::class, 'store']);
Route::put('/UpdateLocality/{id}', [LocalityController::class, 'update']);
Route::delete('/DeleteLocality/{id}', [LocalityController::class, 'destroy']);
