<?php

namespace App\Services\Providers;

class MockAIProvider implements AIProviderInterface
{
    public function extractResumeData(string $rawText): array
    {
        // ... existing code ...
    }

    public function extractJobRequirements(string $jobDescription): array
    {
        // For now, return a sample job requirement structure
        // Later, this will be replaced with actual AI extraction
        
        // Detect if this is a database-related job
        $isDatabaseJob = stripos($jobDescription, 'database') !== false || 
                         stripos($jobDescription, 'sql') !== false ||
                         stripos($jobDescription, 'mysql') !== false;

        if ($isDatabaseJob) {
            return [
                'requirements' => [
                    [
                        'requirement_text' => 'Bachelor\'s degree in Computer Science or related field',
                        'requirement_type' => 'education',
                        'classification' => 'mandatory',
                        'normalized_value' => 'Bachelor\'s in Computer Science',
                        'min_value' => null,
                    ],
                    [
                        'requirement_text' => 'SQL',
                        'requirement_type' => 'skill',
                        'classification' => 'mandatory',
                        'normalized_value' => 'SQL',
                        'min_value' => null,
                    ],
                    [
                        'requirement_text' => 'MySQL',
                        'requirement_type' => 'skill',
                        'classification' => 'mandatory',
                        'normalized_value' => 'MySQL',
                        'min_value' => null,
                    ],
                    [
                        'requirement_text' => 'Database design experience',
                        'requirement_type' => 'skill',
                        'classification' => 'mandatory',
                        'normalized_value' => 'Database Design',
                        'min_value' => null,
                    ],
                    [
                        'requirement_text' => '1+ year of database experience',
                        'requirement_type' => 'experience',
                        'classification' => 'mandatory',
                        'normalized_value' => 'Database Experience',
                        'min_value' => '1 year',
                    ],
                    [
                        'requirement_text' => 'Python',
                        'requirement_type' => 'skill',
                        'classification' => 'preferred',
                        'normalized_value' => 'Python',
                        'min_value' => null,
                    ],
                    [
                        'requirement_text' => 'AWS experience',
                        'requirement_type' => 'skill',
                        'classification' => 'preferred',
                        'normalized_value' => 'AWS',
                        'min_value' => null,
                    ],
                ],
            ];
        }

        // Default fallback
        return [
            'requirements' => [],
        ];
    }
}