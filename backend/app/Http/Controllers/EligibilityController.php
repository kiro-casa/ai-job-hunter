<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Resume;
use App\Services\EligibilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EligibilityController extends Controller
{
    protected EligibilityService $eligibilityService;

    public function __construct(EligibilityService $eligibilityService)
    {
        $this->eligibilityService = $eligibilityService;
    }

    public function check($jobId)
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
                'message' => 'No verified resume found. Please upload and verify your resume first.',
            ], 400);
        }

        $result = $this->eligibilityService->checkEligibility($job, $resume);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }
}