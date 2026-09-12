<?php

namespace App\Services\Providers;

interface AIProviderInterface
{
    /**
     * Extract structured resume data from raw text.
     */
    public function extractResumeData(string $rawText): array;

    /**
     * Extract and classify job requirements from job description.
     * 
     * @param string $jobDescription The full job description text
     * @return array Structured requirements data
     */
    public function extractJobRequirements(string $jobDescription): array;

    public function analyzeSkillGaps(array $missingSkills, string $jobTitle): array;

    public function generateCoverLetter(array $context): array;

    public function generateApplicationAnswers(array $context): array;

    public function generateResumeSuggestions(array $context): array;

    public function generateInterviewPreparation(array $context): array;
}