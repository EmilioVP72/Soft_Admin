<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Payment\PaymentController;

Route::apiResource('/', PaymentController::class)->parameters([
    '' => 'payment'
]);
