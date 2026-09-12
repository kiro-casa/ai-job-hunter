<?php

use Illuminate\Support\Facades\Route;

Route::get('/{any?}', function () {
    $path = public_path('index.html');
    
    if (!file_exists($path)) {
        return response('FRONTEND MISSING AT: ' . $path, 500);
    }
    
    return response(file_get_contents($path), 200, [
        'Content-Type' => 'text/html; charset=UTF-8'
    ]);
})->where('any', '.*');