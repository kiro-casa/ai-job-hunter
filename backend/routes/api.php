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

    // Match Routes
    Route::post('/jobs/{id}/match', [\App\Http\Controllers\MatchController::class, 'calculate']);
    Route::get('/jobs/{id}/match', [\App\Http\Controllers\MatchController::class, 'show']);

    // Eligibility Routes
    Route::post('/jobs/{id}/eligibility/check', [\App\Http\Controllers\EligibilityController::class, 'check']);
    Route::get('/jobs/{id}/eligibility', [\App\Http\Controllers\EligibilityController::class, 'check']); // GET alias for convenience

    // Skill Gap Routes
    Route::post('/jobs/{id}/skill-gaps', [\App\Http\Controllers\SkillGapController::class, 'analyze']);
    Route::get('/jobs/{id}/skill-gaps', [\App\Http\Controllers\SkillGapController::class, 'analyze']);

    // Application Routes
    Route::post('/applications', [\App\Http\Controllers\ApplicationController::class, 'store']);
    Route::get('/applications', [\App\Http\Controllers\ApplicationController::class, 'index']);
    Route::get('/applications/{id}', [\App\Http\Controllers\ApplicationController::class, 'show']);
    Route::patch('/applications/{id}/status', [\App\Http\Controllers\ApplicationController::class, 'updateStatus']);
    Route::post('/applications/{id}/notes', [\App\Http\Controllers\ApplicationController::class, 'addNote']);
    Route::get('/dashboard', [\App\Http\Controllers\ApplicationController::class, 'dashboard']);

    // Automation Routes
    Route::get('/automation/settings', [\App\Http\Controllers\AutomationController::class, 'getSettings']);
    Route::patch('/automation/settings', [\App\Http\Controllers\AutomationController::class, 'updateSettings']);
    Route::post('/applications/{id}/auto-apply', [\App\Http\Controllers\AutomationController::class, 'attemptAutoApply']);

    // Application Assistant Routes
    Route::post('/jobs/{id}/assistant/cover-letter', [\App\Http\Controllers\ApplicationAssistantController::class, 'coverLetter']);
    Route::post('/jobs/{id}/assistant/answers', [\App\Http\Controllers\ApplicationAssistantController::class, 'applicationAnswers']);
    Route::post('/jobs/{id}/assistant/resume-suggestions', [\App\Http\Controllers\ApplicationAssistantController::class, 'resumeSuggestions']);
});