<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'app' => 'AI Job Hunter API',
        'status' => 'running',
        'message' => 'Frontend is hosted on Vercel. This is the API.'
    ]);
});