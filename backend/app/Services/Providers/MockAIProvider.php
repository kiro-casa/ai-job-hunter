<?php

namespace App\Services\Providers;

class MockAIProvider implements AIProviderInterface
{
    public function extractResumeData(string $rawText): array
    {
        // Return structured data based on King Xyro B. Casa's actual resume
        return [
            'profile' => [
                'full_name' => 'King Xyro B. Casa',
                'email' => 'kingxyrocasa@gmail.com',
                'phone' => '(+63) 936-924-1261',
                'location' => 'Cabuyao City, Laguna, Philippines 4025',
                'professional_summary' => 'Computer Science graduate with hands-on experience in database architecture, SQL and MySQL query optimization, data migration, and Quality Assurance (QA) testing. Experienced in relational database design, data integrity, workflow validation, backend troubleshooting, and system verification for enterprise system modules.',
            ],
            'education' => [
                [
                    'degree' => 'Bachelor of Science',
                    'field_of_study' => 'Computer Science',
                    'institution' => 'Pamantasan ng Cabuyao',
                    'start_date' => null,
                    'end_date' => '2026-06-01',
                    'is_current' => false,
                ]
            ],
            'experience' => [
                [
                    'job_title' => 'Database Designer (Database Lead) & Quality Assurance Intern',
                    'company' => 'Ollopa Corporation',
                    'start_date' => '2026-02-01',
                    'end_date' => '2026-04-30',
                    'is_current' => false,
                    'responsibilities' => "Led architectural design of relational database schemas. Executed bulk data migration and optimized SQL/MySQL queries. Troubleshot database mismatches. Designed and executed QA test cases, identified backend bugs, validated workflow logic, and collaborated with developers to verify fixes."
                ]
            ],
            'projects' => [],
            'certifications' => [],
            'languages' => [
                [
                    'language' => 'English',
                    'proficiency' => 'Professional'
                ]
            ],
            'skills' => [
                ['name' => 'SQL', 'category' => 'database', 'proficiency_level' => 'advanced'],
                ['name' => 'MySQL', 'category' => 'database', 'proficiency_level' => 'advanced'],
                ['name' => 'Relational Database Architecture', 'category' => 'database', 'proficiency_level' => 'advanced'],
                ['name' => 'Schema Design', 'category' => 'database', 'proficiency_level' => 'advanced'],
                ['name' => 'Data Migration', 'category' => 'database', 'proficiency_level' => 'intermediate'],
                ['name' => 'Query Optimization', 'category' => 'database', 'proficiency_level' => 'intermediate'],
                ['name' => 'Data Integrity', 'category' => 'database', 'proficiency_level' => 'intermediate'],
                ['name' => 'Data Normalization', 'category' => 'database', 'proficiency_level' => 'intermediate'],
                ['name' => 'Java', 'category' => 'language', 'proficiency_level' => 'intermediate'],
                ['name' => 'JavaScript', 'category' => 'language', 'proficiency_level' => 'intermediate'],
                ['name' => 'HTML', 'category' => 'language', 'proficiency_level' => 'intermediate'],
                ['name' => 'CSS', 'category' => 'language', 'proficiency_level' => 'intermediate'],
                ['name' => 'Backend Web Development', 'category' => 'development', 'proficiency_level' => 'intermediate'],
                ['name' => 'QA Test Case Design', 'category' => 'qa', 'proficiency_level' => 'intermediate'],
                ['name' => 'System Verification', 'category' => 'qa', 'proficiency_level' => 'intermediate'],
                ['name' => 'Bug Isolation', 'category' => 'qa', 'proficiency_level' => 'intermediate'],
                ['name' => 'Workflow Validation', 'category' => 'qa', 'proficiency_level' => 'intermediate'],
                ['name' => 'Git', 'category' => 'tool', 'proficiency_level' => 'intermediate'],
                ['name' => 'GitHub', 'category' => 'tool', 'proficiency_level' => 'intermediate'],
                ['name' => 'Visual Studio Code', 'category' => 'tool', 'proficiency_level' => 'intermediate'],
                ['name' => 'Eclipse', 'category' => 'tool', 'proficiency_level' => 'beginner'],
            ]
        ];
    }

    public function extractJobRequirements(string $jobDescription): array
    {
        // Detect if this is a database-related job
        $isDatabaseJob = stripos($jobDescription, 'database') !== false || 
                         stripos($jobDescription, 'sql') !== false ||
                         stripos($jobDescription, 'mysql') !== false;

        if ($isDatabaseJob) {
            return [
                'requirements' => [
                    [
                        'requirement_text' => 'Bachelor\'s degree in Computer Science, Information Technology, or related field',
                        'requirement_type' => 'education',
                        'classification' => 'mandatory',
                        'normalized_value' => 'Bachelor\'s in Computer Science',
                        'min_value' => null,
                    ],
                    [
                        'requirement_text' => 'Strong proficiency in SQL and MySQL',
                        'requirement_type' => 'skill',
                        'classification' => 'mandatory',
                        'normalized_value' => 'SQL',
                        'min_value' => null,
                    ],
                    [
                        'requirement_text' => 'Strong proficiency in SQL and MySQL',
                        'requirement_type' => 'skill',
                        'classification' => 'mandatory',
                        'normalized_value' => 'MySQL',
                        'min_value' => null,
                    ],
                    [
                        'requirement_text' => 'Experience with relational database design and schema normalization',
                        'requirement_type' => 'skill',
                        'classification' => 'mandatory',
                        'normalized_value' => 'Database Design',
                        'min_value' => null,
                    ],
                    [
                        'requirement_text' => 'At least 1 year of experience in database administration or related role',
                        'requirement_type' => 'experience',
                        'classification' => 'mandatory',
                        'normalized_value' => 'Database Experience',
                        'min_value' => '1 year',
                    ],
                    [
                        'requirement_text' => 'Familiarity with version control systems (Git)',
                        'requirement_type' => 'skill',
                        'classification' => 'mandatory',
                        'normalized_value' => 'Git',
                        'min_value' => null,
                    ],
                    [
                        'requirement_text' => 'Experience with Python or PHP scripting',
                        'requirement_type' => 'skill',
                        'classification' => 'preferred',
                        'normalized_value' => 'Python',
                        'min_value' => null,
                    ],
                    [
                        'requirement_text' => 'Knowledge of cloud platforms (AWS, Azure)',
                        'requirement_type' => 'skill',
                        'classification' => 'preferred',
                        'normalized_value' => 'AWS',
                        'min_value' => null,
                    ],
                ],
            ];
        }

        // Default fallback for non-database jobs
        return [
            'requirements' => [],
        ];
    }

    public function analyzeSkillGaps(array $missingSkills, string $jobTitle): array
{
    // Skill metadata: importance, category, learning difficulty
    $skillMetadata = [
        'python' => [
            'importance' => 'high',
            'category' => 'Programming Language',
            'difficulty' => 'medium',
            'reason' => 'Python is widely used for backend development, data analysis, automation, and scripting. Many database roles require Python for ETL processes and data manipulation.',
            'learning_time' => '2-3 months',
        ],
        'aws' => [
            'importance' => 'high',
            'category' => 'Cloud Platform',
            'difficulty' => 'medium',
            'reason' => 'AWS is the leading cloud platform. Many companies host their databases on AWS RDS, S3, and other services. Cloud skills are increasingly mandatory.',
            'learning_time' => '3-4 months',
        ],
        'postgresql' => [
            'importance' => 'medium',
            'category' => 'Database',
            'difficulty' => 'low',
            'reason' => 'PostgreSQL is a powerful open-source relational database. Since you already know MySQL, PostgreSQL will be easy to learn and expands your database expertise.',
            'learning_time' => '2-3 weeks',
        ],
        'docker' => [
            'importance' => 'medium',
            'category' => 'DevOps Tool',
            'difficulty' => 'medium',
            'reason' => 'Docker is essential for modern deployment and development workflows. Understanding containerization helps with database deployment and testing.',
            'learning_time' => '1-2 months',
        ],
        'azure' => [
            'importance' => 'medium',
            'category' => 'Cloud Platform',
            'difficulty' => 'medium',
            'reason' => 'Azure is Microsoft\'s cloud platform, widely used in enterprise environments. Good to know alongside AWS.',
            'learning_time' => '3-4 months',
        ],
        'php' => [
            'importance' => 'low',
            'category' => 'Programming Language',
            'difficulty' => 'low',
            'reason' => 'PHP is used for backend web development. Since you know JavaScript and backend concepts, PHP will be straightforward.',
            'learning_time' => '1-2 months',
        ],
        'power bi' => [
            'importance' => 'low',
            'category' => 'Data Visualization',
            'difficulty' => 'low',
            'reason' => 'Power BI is used for data visualization and reporting. Useful for data analyst roles but not critical for database administration.',
            'learning_time' => '2-3 weeks',
        ],
    ];

    $gaps = [];
    $priorities = [];

    foreach ($missingSkills as $skill) {
        $normalizedSkill = strtolower(trim($skill));
        $metadata = $skillMetadata[$normalizedSkill] ?? [
            'importance' => 'medium',
            'category' => 'General',
            'difficulty' => 'medium',
            'reason' => "This skill is commonly requested for {$jobTitle} positions.",
            'learning_time' => '1-3 months',
        ];

        $gaps[] = [
            'skill' => $skill,
            'importance' => $metadata['importance'],
            'category' => $metadata['category'],
            'difficulty' => $metadata['difficulty'],
            'reason' => $metadata['reason'],
            'estimated_learning_time' => $metadata['learning_time'],
        ];

        $priorities[] = [
            'skill' => $skill,
            'priority_score' => $this->calculatePriorityScore($metadata['importance'], $metadata['difficulty']),
        ];
    }

    // Sort by priority score (highest first)
    usort($priorities, fn($a, $b) => $b['priority_score'] <=> $a['priority_score']);

    return [
        'skill_gaps' => $gaps,
        'learning_priorities' => $priorities,
        'summary' => $this->generateSkillGapSummary($gaps, $jobTitle),
    ];
}

protected function calculatePriorityScore(string $importance, string $difficulty): int
{
    $importanceScores = ['high' => 3, 'medium' => 2, 'low' => 1];
    $difficultyScores = ['low' => 3, 'medium' => 2, 'high' => 1]; // Easier = higher priority

    return ($importanceScores[$importance] ?? 2) * 10 + ($difficultyScores[$difficulty] ?? 2);
}

protected function generateSkillGapSummary(array $gaps, string $jobTitle): string
{
    $highPriority = array_filter($gaps, fn($g) => $g['importance'] === 'high');
    $mediumPriority = array_filter($gaps, fn($g) => $g['importance'] === 'medium');
    $lowPriority = array_filter($gaps, fn($g) => $g['importance'] === 'low');

    $summary = "For a {$jobTitle} role, you have " . count($gaps) . " skill gaps to address.\n\n";

    if (count($highPriority) > 0) {
        $summary .= "High Priority: " . implode(', ', array_column($highPriority, 'skill')) . ". These are critical for most " . $jobTitle . " positions.\n\n";
    }

    if (count($mediumPriority) > 0) {
        $summary .= "Medium Priority: " . implode(', ', array_column($mediumPriority, 'skill')) . ". These will make you more competitive.\n\n";
    }

    if (count($lowPriority) > 0) {
        $summary .= "Nice to Have: " . implode(', ', array_column($lowPriority, 'skill')) . ". These are optional but beneficial.\n\n";
    }

    $summary .= "Focus on high-priority skills first, then work through the list based on your career goals.";

    return $summary;
}
}