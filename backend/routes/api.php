<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'data' => [
            'status' => 'ok',
            'message' => 'AI Job Hunter API is running',
            'timestamp' => now()->toIso8601String(),
        ],
    ]);
});