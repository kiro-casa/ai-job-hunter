<?php

namespace App\Services;

use App\Models\Job;
use App\Models\Resume;
use App\Services\AIService;

class SkillGapService
{
    protected AIService $aiService;
    protected EligibilityService $eligibilityService;

    public function __construct(AIService $aiService, EligibilityService $eligibilityService)
    {
        $this->aiService = $aiService;
        $this->eligibilityService = $eligibilityService;
    }

    public function analyzeGaps(Job $job, Resume $resume): array
    {
        // Get eligibility data to find missing skills
        $eligibilityResult = $this->eligibilityService->checkEligibility($job, $resume);

        // Combine missing required and preferred skills
        $allMissingSkills = array_merge(
            $eligibilityResult['missing_required'],
            $eligibilityResult['missing_preferred']
        );

        if (empty($allMissingSkills)) {
            return [
                'success' => true,
                'data' => [
                    'skill_gaps' => [],
                    'learning_priorities' => [],
                    'summary' => 'No skill gaps identified! You meet all requirements for this position.',
                ],
            ];
        }

        // Get AI-powered skill gap analysis
        $analysis = $this->aiService->analyzeSkillGaps($allMissingSkills, $job->title);

        return [
            'success' => true,
            'data' => $analysis,
        ];
    }
}