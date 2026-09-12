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

        public function generateCoverLetter(array $context): array
    {
        $resume = $context['resume'];
        $job = $context['job'];
        $profile = $resume->profile;
        $latestExperience = $resume->experiences->first();
        $latestEducation = $resume->educations->first();

        $fullName = $profile->full_name ?? 'Applicant';
        $email = $profile->email ?? '';
        $phone = $profile->phone ?? '';
        $summary = $profile->professional_summary ?? '';
        $jobTitle = $job->title;
        $company = $job->company;

        $experienceParagraph = '';
        if ($latestExperience) {
            $experienceParagraph = "Most recently, I served as {$latestExperience->job_title} at {$latestExperience->company}, where {$latestExperience->responsibilities}";
        }

        $educationParagraph = '';
        if ($latestEducation) {
            $educationParagraph = "I hold a {$latestEducation->degree} in {$latestEducation->field_of_study} from {$latestEducation->institution}.";
        }

        $coverLetter = "Dear Hiring Manager,\n\n" .
            "I am writing to express my strong interest in the {$jobTitle} position at {$company}. " .
            "{$summary}\n\n" .
            "{$experienceParagraph}\n\n" .
            "{$educationParagraph}\n\n" .
            "I am particularly drawn to this opportunity at {$company} because it aligns perfectly with my technical background and career aspirations. " .
            "I am confident that my combination of hands-on experience and academic foundation would allow me to contribute meaningfully to your team from day one.\n\n" .
            "I would welcome the opportunity to discuss how my skills and experiences align with your needs. Thank you for considering my application.\n\n" .
            "Sincerely,\n{$fullName}\n{$email}\n{$phone}";

        return [
            'cover_letter' => $coverLetter,
            'word_count' => str_word_count($coverLetter),
            'tone' => 'professional',
            'personalization_level' => 'high',
        ];
    }

    public function generateApplicationAnswers(array $context): array
    {
        $resume = $context['resume'];
        $job = $context['job'];
        $profile = $resume->profile;
        $latestExperience = $resume->experiences->first();

        $fullName = $profile->full_name ?? 'Applicant';
        $summary = $profile->professional_summary ?? '';

        $questions = [
            [
                'question' => 'Why are you interested in this position?',
                'answer' => "I am drawn to the {$job->title} role at {$job->company} because it directly aligns with my expertise in database architecture and quality assurance. {$summary}",
                'category' => 'motivation',
            ],
            [
                'question' => 'What are your greatest strengths?',
                'answer' => "My greatest strengths are my analytical approach to database design and my meticulous attention to detail in QA testing. During my time at Ollopa Corporation, I led the architectural design of relational database schemas while simultaneously executing QA test cases to ensure data integrity and workflow validation.",
                'category' => 'strengths',
            ],
            [
                'question' => 'Tell me about a challenging project you worked on.',
                'answer' => $latestExperience 
                    ? "At {$latestExperience->company}, I led the architectural design of relational database schemas and executed bulk data migration. One of the biggest challenges was troubleshooting database mismatches during migration while ensuring zero data loss. I optimized SQL/MySQL queries and collaborated closely with developers to verify fixes, ultimately delivering a stable, high-performance database system."
                    : "I led the architectural design of relational database schemas during my internship, where I optimized SQL queries and executed bulk data migrations while ensuring data integrity.",
                'category' => 'experience',
            ],
            [
                'question' => 'Where do you see yourself in 5 years?',
                'answer' => "In five years, I see myself growing into a senior database architect or backend engineering role, where I can lead larger-scale system designs and mentor junior developers. I am particularly interested in deepening my expertise in cloud database solutions and distributed systems.",
                'category' => 'goals',
            ],
        ];

        return [
            'questions' => $questions,
            'total_questions' => count($questions),
        ];
    }

    public function generateResumeSuggestions(array $context): array
    {
        $job = $context['job'];
        $jobRequirements = $context['job_requirements'] ?? [];

        $suggestions = [];

        // Analyze job requirements and generate targeted suggestions
        foreach ($jobRequirements as $req) {
            if ($req['requirement_type'] === 'skill') {
                $normalizedValue = strtolower($req['normalized_value'] ?? '');
                
                if (in_array($normalizedValue, ['python', 'aws', 'azure', 'docker'])) {
                    $suggestions[] = [
                        'type' => 'add_skill',
                        'priority' => $req['classification'] === 'mandatory' ? 'high' : 'medium',
                        'suggestion' => "Consider adding '{$req['normalized_value']}' to your skills section if you have any exposure to it, even at a beginner level.",
                        'reason' => "This is a {$req['classification']} requirement for the {$job->title} role.",
                    ];
                }
            }

            if ($req['requirement_type'] === 'experience') {
                $suggestions[] = [
                    'type' => 'emphasize_experience',
                    'priority' => $req['classification'] === 'mandatory' ? 'high' : 'medium',
                    'suggestion' => "Quantify your experience with specific metrics (e.g., 'optimized queries reducing load time by 40%', 'migrated 10,000+ records with 99.9% accuracy').",
                    'reason' => "The job requires {$req['normalized_value']}. Concrete numbers make your experience more compelling.",
                ];
            }
        }

        // General suggestions based on job type
        $jobDesc = strtolower($job->description);
        
        if (str_contains($jobDesc, 'team') || str_contains($jobDesc, 'collaborate')) {
            $suggestions[] = [
                'type' => 'soft_skill',
                'priority' => 'medium',
                'suggestion' => "Highlight your collaboration experience. Add bullet points about working with cross-functional teams.",
                'reason' => "The job description emphasizes teamwork and collaboration.",
            ];
        }

        if (str_contains($jobDesc, 'lead') || str_contains($jobDesc, 'senior')) {
            $suggestions[] = [
                'type' => 'leadership',
                'priority' => 'high',
                'suggestion' => "Emphasize leadership moments from your internship, such as leading database architecture design or coordinating QA efforts.",
                'reason' => "The role appears to value leadership qualities.",
            ];
        }

        // Always add a general suggestion
        $suggestions[] = [
            'type' => 'keywords',
            'priority' => 'medium',
            'suggestion' => "Mirror the exact terminology used in the job description. If they say 'relational database design', use that exact phrase instead of 'database architecture'.",
            'reason' => "ATS systems and recruiters scan for exact keyword matches.",
        ];

        // Sort by priority
        usort($suggestions, fn($a, $b) => 
            ($b['priority'] === 'high' ? 3 : ($b['priority'] === 'medium' ? 2 : 1)) - 
            ($a['priority'] === 'high' ? 3 : ($a['priority'] === 'medium' ? 2 : 1))
        );

        return [
            'suggestions' => $suggestions,
            'total_suggestions' => count($suggestions),
            'job_title' => $job->title,
        ];
    }
}