<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Services\InterviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InterviewController extends Controller
{
    protected InterviewService $interviewService;

    public function __construct(InterviewService $interviewService)
    {
        $this->interviewService = $interviewService;
    }

    public function prepare($jobId)
    {
        $job = Auth::user()->jobs()->findOrFail($jobId);
        
        $resume = Auth::user()->resumes()
            ->with(['profile', 'educations', 'experiences', 'skills'])
            ->where('status', 'verified')
            ->latest()
            ->first();

        if (!$resume) {
            return response()->json([
                'success' => false,
                'message' => 'No verified resume found.',
            ], 400);
        }

        $result = $this->interviewService->generatePreparation($job, $resume);
        return response()->json($result);
    }
}