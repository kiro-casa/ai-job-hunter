<?php

use Illuminate\Support\Facades\Route;

// Serve the React app for the root URL
Route::get('/', function () {
    return response()->file(public_path('index.html'), [
        'Content-Type' => 'text/html',
    ]);
});

// Serve the React app for all other frontend routes (like /login, /dashboard)
Route::get('/{any}', function () {
    return response()->file(public_path('index.html'), [
        'Content-Type' => 'text/html',
    ]);
})->where('any', '.*');