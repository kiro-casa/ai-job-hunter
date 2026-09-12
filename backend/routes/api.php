<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;

// Health check (public)
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

// Authentication routes (public)
Route::post('/auth/register', [RegisterController::class, 'store']);
Route::post('/auth/login', [LoginController::class, 'store']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [LogoutController::class, 'store']);
    
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
        ]);
    });
    Route::post('/resumes', [\App\Http\Controllers\ResumeController::class, 'store']);
    Route::get('/resumes', [\App\Http\Controllers\ResumeController::class, 'index']);
    Route::get('/resumes/{id}', [\App\Http\Controllers\ResumeController::class, 'show']);
    Route::post('/resumes/{id}/verify', [\App\Http\Controllers\ResumeController::class, 'verify']);

    Route::post('/jobs', [\App\Http\Controllers\JobController::class, 'store']);
    Route::get('/jobs', [\App\Http\Controllers\JobController::class, 'index']);
    Route::get('/jobs/{id}', [\App\Http\Controllers\JobController::class, 'show']);
});