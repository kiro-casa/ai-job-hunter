<?php

namespace App\Services;

use App\Models\Job;
use App\Models\Resume;
use App\Services\AIService;

class ApplicationAssistantService
{
    protected AIService $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function generateCoverLetter(Job $job, Resume $resume): array
    {
        try {
            $result = $this->aiService->generateCoverLetter([
                'job' => $job,
                'resume' => $resume,
            ]);

            return [
                'success' => true,
                'data' => $result,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to generate cover letter: ' . $e->getMessage(),
            ];
        }
    }

    public function generateApplicationAnswers(Job $job, Resume $resume): array
    {
        try {
            $result = $this->aiService->generateApplicationAnswers([
                'job' => $job,
                'resume' => $resume,
            ]);

            return [
                'success' => true,
                'data' => $result,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to generate answers: ' . $e->getMessage(),
            ];
        }
    }

    public function generateResumeSuggestions(Job $job, Resume $resume): array
    {
        try {
            $jobRequirements = $job->requirements->map(fn($r) => [
                'requirement_text' => $r->requirement_text,
                'requirement_type' => $r->requirement_type,
                'classification' => $r->classification,
                'normalized_value' => $r->normalized_value,
            ])->toArray();

            $result = $this->aiService->generateResumeSuggestions([
                'job' => $job,
                'resume' => $resume,
                'job_requirements' => $jobRequirements,
            ]);

            return [
                'success' => true,
                'data' => $result,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to generate suggestions: ' . $e->getMessage(),
            ];
        }
    }
}