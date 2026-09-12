<?php

namespace App\Services;

use App\Models\Job;
use App\Models\Resume;
use App\Services\AIService;

class InterviewService
{
    protected AIService $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function generatePreparation(Job $job, Resume $resume): array
    {
        try {
            $result = $this->aiService->generateInterviewPreparation([
                'job' => $job->load('requirements'),
                'resume' => $resume->load(['profile', 'educations', 'experiences', 'skills']),
            ]);

            return [
                'success' => true,
                'data' => $result,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to generate interview preparation: ' . $e->getMessage(),
            ];
        }
    }
}