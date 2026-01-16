<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TestController;

Route::prefix('api')->group(function () {
    Route::get('/test', [TestController::class, 'index']);
});
