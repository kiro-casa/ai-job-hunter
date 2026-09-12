<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Resume;
use App\Services\MatchingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MatchController extends Controller
{
    protected MatchingService $matchingService;

    public function __construct(MatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    }

    public function calculate($jobId)
    {
        $job = Auth::user()->jobs()->with('requirements')->findOrFail($jobId);
        
        $resume = Auth::user()->resumes()
            ->with(['skills', 'educations', 'experiences'])
            ->where('status', 'verified')
            ->latest()
            ->first();

        if (!$resume) {
            return response()->json([
                'success' => false,
                'message' => 'No verified resume found. Please upload and verify your resume first.',
            ], 400);
        }

        $result = $this->matchingService->calculateMatch($job, $resume);

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    public function show($jobId)
    {
        $resume = Auth::user()->resumes()
            ->where('status', 'verified')
            ->latest()
            ->first();

        if (!$resume) {
            return response()->json([
                'success' => false,
                'message' => 'No verified resume found.',
            ], 400);
        }

        $match = \App\Models\JobMatch::where('user_id', Auth::id())
            ->where('job_id', $jobId)
            ->where('resume_id', $resume->id)
            ->with('components')
            ->first();

        if (!$match) {
            return response()->json([
                'success' => false,
                'message' => 'No match calculated yet. Please calculate the match first.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $match,
        ]);
    }
}