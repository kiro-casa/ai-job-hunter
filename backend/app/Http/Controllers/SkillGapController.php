<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Resume;
use App\Services\SkillGapService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkillGapController extends Controller
{
    protected SkillGapService $skillGapService;

    public function __construct(SkillGapService $skillGapService)
    {
        $this->skillGapService = $skillGapService;
    }

    public function analyze($jobId)
    {
        $job = Auth::user()->jobs()->findOrFail($jobId);
        
        $resume = Auth::user()->resumes()
            ->with(['skills', 'educations', 'experiences'])
            ->where('status', 'verified')
            ->latest()
            ->first();

        if (!$resume) {
            return response()->json([
                'success' => false,
                'message' => 'No verified resume found.',
            ], 400);
        }

        $result = $this->skillGapService->analyzeGaps($job, $resume);

        return response()->json($result);
    }
}