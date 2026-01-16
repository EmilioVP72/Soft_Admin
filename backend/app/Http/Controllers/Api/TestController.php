<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class TestController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'message' => 'Laravel 11 y Vue 3 conectados correctamente 🚀'
        ]);
    }
}
