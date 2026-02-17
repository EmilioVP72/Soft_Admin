<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Store\StoreController;

/*
|--------------------------------------------------------------------------
| API Routes Store
|--------------------------------------------------------------------------
|*/

Route::get('/all', [StoreController::class, 'index']);
Route::get('/OneStore/{id}', [StoreController::class, 'show']);
Route::post('/StoreStore', [StoreController::class, 'store']);
Route::put('/UpdateStore/{id}', [StoreController::class, 'update']);
Route::delete('/DeleteStore/{id}', [StoreController::class, 'destroy']);
