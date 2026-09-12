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
}