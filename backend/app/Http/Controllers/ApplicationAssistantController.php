<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Resume;
use App\Services\ApplicationAssistantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationAssistantController extends Controller
{
    protected ApplicationAssistantService $assistantService;

    public function __construct(ApplicationAssistantService $assistantService)
    {
        $this->assistantService = $assistantService;
    }

    public function coverLetter($jobId)
    {
        $job = Auth::user()->jobs()->findOrFail($jobId);
        $resume = $this->getVerifiedResume();

        if (!$resume) {
            return response()->json([
                'success' => false,
                'message' => 'No verified resume found.',
            ], 400);
        }

        $result = $this->assistantService->generateCoverLetter($job, $resume);
        return response()->json($result);
    }

    public function applicationAnswers($jobId)
    {
        $job = Auth::user()->jobs()->findOrFail($jobId);
        $resume = $this->getVerifiedResume();

        if (!$resume) {
            return response()->json([
                'success' => false,
                'message' => 'No verified resume found.',
            ], 400);
        }

        $result = $this->assistantService->generateApplicationAnswers($job, $resume);
        return response()->json($result);
    }

    public function resumeSuggestions($jobId)
    {
        $job = Auth::user()->jobs()->with('requirements')->findOrFail($jobId);
        $resume = $this->getVerifiedResume();

        if (!$resume) {
            return response()->json([
                'success' => false,
                'message' => 'No verified resume found.',
            ], 400);
        }

        $result = $this->assistantService->generateResumeSuggestions($job, $resume);
        return response()->json($result);
    }

    protected function getVerifiedResume()
    {
        return Auth::user()->resumes()
            ->with(['profile', 'educations', 'experiences', 'skills'])
            ->where('status', 'verified')
            ->latest()
            ->first();
    }
}