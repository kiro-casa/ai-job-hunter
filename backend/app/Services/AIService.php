<?php

namespace App\Services;

use App\Services\Providers\AIProviderInterface;

class AIService
{
    protected AIProviderInterface $provider;

    public function __construct(AIProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    public function extractResumeData(string $rawText): array
    {
        return $this->provider->extractResumeData($rawText);
    }

    public function extractJobRequirements(string $jobDescription): array
    {
        return $this->provider->extractJobRequirements($jobDescription);
    }

    public function analyzeSkillGaps(array $missingSkills, string $jobTitle): array
    {
        return $this->provider->analyzeSkillGaps($missingSkills, $jobTitle);
    }

    public function generateCoverLetter(array $context): array
    {
        return $this->provider->generateCoverLetter($context);
    }

    public function generateApplicationAnswers(array $context): array
    {
        return $this->provider->generateApplicationAnswers($context);
    }

    public function generateResumeSuggestions(array $context): array
    {
        return $this->provider->generateResumeSuggestions($context);
    }
}