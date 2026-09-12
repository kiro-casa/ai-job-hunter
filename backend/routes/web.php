<?php

use Illuminate\Support\Facades\Route;

// Catch-all route to serve the React frontend
Route::get('/{any}', function () {
    $indexPath = public_path('index.html');
    
    if (file_exists($indexPath)) {
        return file_get_contents($indexPath);
    }
    
    return response()->view('errors.500', [
        'message' => 'Frontend build (index.html) is missing from the public folder.'
    ], 500);
})->where('any', '.*');