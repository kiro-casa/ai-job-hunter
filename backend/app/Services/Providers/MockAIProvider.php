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

        public function generateInterviewPreparation(array $context): array
    {
        $job = $context['job'];
        $resume = $context['resume'];
        $jobDesc = strtolower($job->description);
        
        $profile = $resume->profile;
        $latestExperience = $resume->experiences->first();
        
        // Determine job category
        $isDatabaseRole = str_contains($jobDesc, 'database') || str_contains($jobDesc, 'sql') || str_contains($jobDesc, 'dba');
        $isQARole = str_contains($jobDesc, 'qa') || str_contains($jobDesc, 'quality assurance') || str_contains($jobDesc, 'testing');
        $isBackendRole = str_contains($jobDesc, 'backend') || str_contains($jobDesc, 'api') || str_contains($jobDesc, 'server');
        
        // Behavioral Questions (always relevant)
        $behavioralQuestions = [
            [
                'question' => 'Tell me about yourself.',
                'category' => 'behavioral',
                'difficulty' => 'easy',
                'likelihood' => 'very_high',
                'star_template' => null,
                'coaching_tips' => [
                    'Keep it to 2 minutes max',
                    'Focus on professional journey, not personal life',
                    'End with why you\'re interested in this role',
                ],
                'sample_answer' => "I'm a Computer Science graduate from Pamantasan ng Cabuyao with hands-on experience in database architecture and QA testing. During my internship at Ollopa Corporation, I led the architectural design of relational database schemas and executed QA test cases for enterprise system modules. I'm passionate about building robust data systems and ensuring data integrity, which is why I'm excited about this opportunity.",
            ],
            [
                'question' => 'Why do you want to work here?',
                'category' => 'behavioral',
                'difficulty' => 'easy',
                'likelihood' => 'very_high',
                'star_template' => null,
                'coaching_tips' => [
                    'Research the company beforehand',
                    'Connect their mission to your values',
                    'Be specific, not generic',
                ],
                'sample_answer' => "I'm drawn to {$job->company} because of your focus on [specific company value/project from research]. My experience in database architecture and QA aligns perfectly with what you're building, and I'm excited about the opportunity to contribute to [specific team/project].",
            ],
            [
                'question' => 'Tell me about a time you faced a challenge at work.',
                'category' => 'behavioral',
                'difficulty' => 'medium',
                'likelihood' => 'very_high',
                'star_template' => [
                    'situation' => 'Describe the context and background',
                    'task' => 'Explain your responsibility',
                    'action' => 'Detail what you specifically did',
                    'result' => 'Share the outcome with metrics if possible',
                ],
                'coaching_tips' => [
                    'Use the STAR method strictly',
                    'Focus on YOUR actions, not the team\'s',
                    'Quantify the result when possible',
                    'Choose a relevant challenge',
                ],
                'sample_answer' => $latestExperience 
                    ? "At {$latestExperience->company}, we faced a critical data migration challenge where database mismatches threatened to delay the project. I took ownership of troubleshooting the root cause, optimized the SQL queries causing the discrepancies, and collaborated with developers to verify fixes. As a result, we completed the migration on time with 99.9% data accuracy."
                    : "During my internship, I encountered a complex database mismatch issue during a data migration project. I systematically diagnosed the root cause, optimized the problematic queries, and worked with the development team to implement fixes. The migration was completed successfully with zero data loss.",
            ],
            [
                'question' => 'What is your greatest weakness?',
                'category' => 'behavioral',
                'difficulty' => 'medium',
                'likelihood' => 'high',
                'star_template' => null,
                'coaching_tips' => [
                    'Choose a real but non-critical weakness',
                    'Show self-awareness',
                    'Explain how you\'re working to improve',
                    'Never say "I have no weaknesses"',
                ],
                'sample_answer' => "Early in my career, I tended to dive deep into technical details without always considering the bigger business picture. I've been actively working on this by asking more questions about business goals before starting technical work, and by regularly checking in with stakeholders to ensure alignment. This has helped me deliver more impactful solutions.",
            ],
        ];
        
        // Technical Questions (role-specific)
        $technicalQuestions = [];
        
        if ($isDatabaseRole) {
            $technicalQuestions = [
                [
                    'question' => 'Explain the difference between INNER JOIN and LEFT JOIN.',
                    'category' => 'technical',
                    'difficulty' => 'easy',
                    'likelihood' => 'very_high',
                    'star_template' => null,
                    'coaching_tips' => [
                        'Use a real example',
                        'Explain when you\'d use each',
                        'Mention performance implications',
                    ],
                    'sample_answer' => "An INNER JOIN returns only rows that have matching values in both tables, while a LEFT JOIN returns all rows from the left table and matching rows from the right table, with NULLs where there's no match. In my work at Ollopa Corporation, I used INNER JOINs when I needed only complete records, and LEFT JOINs when I needed to preserve all records from a primary table even if related data was missing.",
                ],
                [
                    'question' => 'How do you optimize a slow SQL query?',
                    'category' => 'technical',
                    'difficulty' => 'medium',
                    'likelihood' => 'very_high',
                    'star_template' => null,
                    'coaching_tips' => [
                        'Walk through your systematic approach',
                        'Mention EXPLAIN plans',
                        'Talk about indexing strategies',
                        'Give a real example',
                    ],
                    'sample_answer' => "I follow a systematic approach: First, I use EXPLAIN to analyze the query execution plan and identify bottlenecks. Then I check if proper indexes exist on columns used in WHERE, JOIN, and ORDER BY clauses. I also look for unnecessary subqueries that could be rewritten as JOINs, and check for N+1 query problems. At Ollopa Corporation, I optimized a query that was taking 30 seconds down to under 1 second by adding a composite index and rewriting a correlated subquery.",
                ],
                [
                    'question' => 'What is database normalization? Explain the first three normal forms.',
                    'category' => 'technical',
                    'difficulty' => 'medium',
                    'likelihood' => 'high',
                    'star_template' => null,
                    'coaching_tips' => [
                        'Use concrete examples',
                        'Explain WHY normalization matters',
                        'Mention when denormalization is appropriate',
                    ],
                    'sample_answer' => "Database normalization is the process of organizing data to reduce redundancy and improve data integrity. First Normal Form requires atomic values and no repeating groups. Second Normal Form requires the table to be in 1NF and all non-key columns to be fully dependent on the primary key. Third Normal Form requires the table to be in 2NF and have no transitive dependencies. In my schema design work, I always normalize to 3NF as a baseline, then selectively denormalize for performance where needed.",
                ],
                [
                    'question' => 'How do you handle database migrations in a production environment?',
                    'category' => 'technical',
                    'difficulty' => 'hard',
                    'likelihood' => 'high',
                    'star_template' => null,
                    'coaching_tips' => [
                        'Emphasize safety and rollback plans',
                        'Mention backup strategies',
                        'Talk about zero-downtime migrations',
                        'Reference your real experience',
                    ],
                    'sample_answer' => "I follow a careful process: First, I always create a full backup before any migration. Then I test the migration in a staging environment that mirrors production. I write rollback scripts before executing anything. During execution, I prefer incremental migrations during low-traffic periods. At Ollopa Corporation, I executed bulk data migrations with a 99.9% success rate by following this methodology and maintaining constant communication with the team.",
                ],
            ];
        }
        
        if ($isQARole) {
            $technicalQuestions[] = [
                'question' => 'How do you design effective test cases?',
                'category' => 'technical',
                'difficulty' => 'medium',
                'likelihood' => 'very_high',
                'star_template' => null,
                'coaching_tips' => [
                    'Mention equivalence partitioning',
                    'Talk about boundary value analysis',
                    'Include both positive and negative tests',
                ],
                'sample_answer' => "I use a combination of techniques: equivalence partitioning to group similar inputs, boundary value analysis to test edge cases, and decision tables for complex logic. I always include both positive tests (valid inputs) and negative tests (invalid inputs, edge cases). At Ollopa Corporation, I designed comprehensive test cases that caught critical backend bugs before they reached production.",
            ];
        }
        
        // Situational Questions
        $situationalQuestions = [
            [
                'question' => 'How do you handle tight deadlines?',
                'category' => 'situational',
                'difficulty' => 'medium',
                'likelihood' => 'high',
                'star_template' => [
                    'situation' => 'Describe the deadline pressure',
                    'task' => 'What you needed to accomplish',
                    'action' => 'How you prioritized and executed',
                    'result' => 'The outcome',
                ],
                'coaching_tips' => [
                    'Show your prioritization skills',
                    'Mention communication with stakeholders',
                    'Demonstrate calmness under pressure',
                ],
                'sample_answer' => "When facing tight deadlines, I first break down the work into priorities using the Eisenhower Matrix. I communicate early with stakeholders about what's realistic, focus on delivering the highest-value items first, and ask for help when needed. In my internship, I successfully delivered a critical database migration on a compressed timeline by breaking it into phases and maintaining daily check-ins with the team.",
            ],
            [
                'question' => 'Tell me about a time you disagreed with a team member.',
                'category' => 'situational',
                'difficulty' => 'medium',
                'likelihood' => 'medium',
                'star_template' => [
                    'situation' => 'The disagreement context',
                    'task' => 'What needed to be resolved',
                    'action' => 'How you handled it professionally',
                    'result' => 'The outcome and what you learned',
                ],
                'coaching_tips' => [
                    'Stay professional, don\'t blame',
                    'Focus on the issue, not the person',
                    'Show what you learned',
                ],
                'sample_answer' => "During a project, a colleague and I disagreed on the database schema design approach. Instead of insisting on my way, I suggested we both prototype our solutions and compare them objectively based on performance and maintainability metrics. We ended up combining the best elements of both approaches, which resulted in a stronger final design. This taught me the value of collaborative problem-solving.",
            ],
        ];
        
        // Company-specific questions (generic since we don't have real company data)
        $companyQuestions = [
            [
                'question' => "What do you know about {$job->company}?",
                'category' => 'company',
                'difficulty' => 'easy',
                'likelihood' => 'very_high',
                'star_template' => null,
                'coaching_tips' => [
                    'Research their website thoroughly',
                    'Know their recent news/products',
                    'Understand their competitors',
                    'Connect to why you want to work there',
                ],
                'sample_answer' => "[Research {$job->company} before the interview. Mention their mission, recent projects, company culture, and how your skills align with their current needs.]",
            ],
            [
                'question' => 'Do you have any questions for us?',
                'category' => 'company',
                'difficulty' => 'easy',
                'likelihood' => 'very_high',
                'star_template' => null,
                'coaching_tips' => [
                    'Always have 3-5 questions prepared',
                    'Ask about team structure and culture',
                    'Inquire about growth opportunities',
                    'Avoid questions easily answered on their website',
                ],
                'sample_answer' => "Yes, I have a few questions: 1) What does success look like in this role after 6 months? 2) Can you tell me about the team I'd be working with? 3) What are the biggest challenges the team is currently facing? 4) How does the company support professional development?",
            ],
        ];
        
        $allQuestions = array_merge(
            $behavioralQuestions, 
            $technicalQuestions, 
            $situationalQuestions, 
            $companyQuestions
        );
        
        // Calculate preparation priority
        foreach ($allQuestions as &$q) {
            $likelihoodScore = match($q['likelihood']) {
                'very_high' => 3,
                'high' => 2,
                'medium' => 1,
                default => 0,
            };
            $difficultyScore = match($q['difficulty']) {
                'hard' => 3,
                'medium' => 2,
                'easy' => 1,
                default => 0,
            };
            $q['preparation_priority'] = $likelihoodScore * $difficultyScore;
        }
        
        // Sort by preparation priority
        usort($allQuestions, fn($a, $b) => $b['preparation_priority'] <=> $a['preparation_priority']);
        
        return [
            'questions' => $allQuestions,
            'total_questions' => count($allQuestions),
            'job_title' => $job->title,
            'company' => $job->company,
            'preparation_summary' => $this->generatePreparationSummary($allQuestions),
        ];
    }
    
    protected function generatePreparationSummary(array $questions): string
    {
        $veryHighLikelihood = count(array_filter($questions, fn($q) => $q['likelihood'] === 'very_high'));
        $hardQuestions = count(array_filter($questions, fn($q) => $q['difficulty'] === 'hard'));
        $behavioral = count(array_filter($questions, fn($q) => $q['category'] === 'behavioral'));
        $technical = count(array_filter($questions, fn($q) => $q['category'] === 'technical'));
        
        return "You have {$veryHighLikelihood} very likely questions to prepare for, including {$hardQuestions} challenging ones. " .
               "Focus first on behavioral questions ({$behavioral} total) since they're almost guaranteed, then tackle technical questions ({$technical} total). " .
               "Use the STAR method for all behavioral and situational questions.";
    }
}