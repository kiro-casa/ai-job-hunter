<?php

namespace App\Services;

use App\Models\Job;
use App\Models\Resume;
use App\Models\JobMatch;
use App\Models\MatchComponent;
use App\Models\JobRequirementMatch;
use App\Models\Skill;
use Illuminate\Support\Facades\DB;

class MatchingService
{
    // Scoring weights (must sum to 1.0)
    const WEIGHT_SKILLS = 0.30;
    const WEIGHT_EXPERIENCE = 0.25;
    const WEIGHT_RESPONSIBILITIES = 0.15;
    const WEIGHT_EDUCATION = 0.10;
    const WEIGHT_KEYWORDS = 0.10;
    const WEIGHT_PREFERENCES = 0.10;

    // Skill match scores
    const SKILL_EXACT = 1.0;
    const SKILL_EQUIVALENT = 0.9;
    const SKILL_RELATED = 0.5;
    const SKILL_PARTIAL = 0.3;
    const SKILL_MISSING = 0.0;

    // Known skill equivalencies
    const EQUIVALENCIES = [
        'my sql' => 'mysql',
        'my_sql' => 'mysql',
        'ms sql' => 'mssql',
        'ms_sql' => 'mssql',
        'sql server' => 'mssql',
        'node.js' => 'nodejs',
        'node js' => 'nodejs',
        'react.js' => 'react',
        'react js' => 'react',
        'vue.js' => 'vue',
        'vue js' => 'vue',
        'express.js' => 'express',
        'typescript' => 'typescript',
        'ts' => 'typescript',
        'javascript' => 'javascript',
        'js' => 'javascript',
        'html5' => 'html',
        'css3' => 'css',
        'postgresql' => 'postgresql',
        'postgres' => 'postgresql',
        'rdbms' => 'relational database architecture',
        'relational database management system' => 'relational database architecture',
        'relational database management systems' => 'relational database architecture',
        'dbms' => 'relational database architecture',
        'database design' => 'schema design',
        'schema design' => 'schema design',
        'database architecture' => 'relational database architecture',
        'git' => 'git',
        'github' => 'github',
        'vscode' => 'visual studio code',
        'visual studio code' => 'visual studio code',
        'vs code' => 'visual studio code',
        'qa' => 'qa test case design',
        'quality assurance' => 'qa test case design',
        'qa testing' => 'qa test case design',
    ];

    // Known related skills (same category, different technology)
    const RELATED_SKILLS = [
        'mysql' => ['postgresql', 'mssql', 'oracle', 'sqlite', 'mariadb'],
        'postgresql' => ['mysql', 'mssql', 'oracle', 'sqlite', 'mariadb'],
        'mssql' => ['mysql', 'postgresql', 'oracle', 'sqlite'],
        'java' => ['c#', 'c++', 'kotlin', 'scala'],
        'javascript' => ['typescript'],
        'typescript' => ['javascript'],
        'python' => ['ruby', 'perl', 'php'],
        'php' => ['python', 'ruby', 'perl'],
        'react' => ['vue', 'angular', 'svelte'],
        'vue' => ['react', 'angular', 'svelte'],
        'angular' => ['react', 'vue', 'svelte'],
        'aws' => ['azure', 'gcp', 'google cloud'],
        'azure' => ['aws', 'gcp', 'google cloud'],
        'docker' => ['kubernetes', 'podman'],
        'git' => ['github', 'gitlab', 'bitbucket'],
        'github' => ['git', 'gitlab', 'bitbucket'],
    ];

    // Skills that are NOT equivalent (common false positives)
    const NOT_EQUIVALENT = [
        'java' => ['javascript'],
        'javascript' => ['java'],
        'sql' => ['python', 'java', 'javascript'],
        'python' => ['sql', 'java'],
        'c' => ['c++', 'c#'],
        'c++' => ['c', 'c#'],
    ];

    public function calculateMatch(Job $job, Resume $resume): array
    {
        DB::beginTransaction();

        try {
            // Get job requirements and resume data
            $jobRequirements = $job->requirements;
            $resumeSkills = $resume->skills->pluck('normalized_name')->map(fn($s) => strtolower($s))->toArray();
            $resumeEducations = $resume->educations;
            $resumeExperiences = $resume->experiences;

            // 1. Calculate Skills Score
            $skillsResult = $this->calculateSkillsScore($jobRequirements, $resumeSkills);

            // 2. Calculate Experience Score
            $experienceResult = $this->calculateExperienceScore($jobRequirements, $resumeExperiences);

            // 3. Calculate Responsibilities Score
            $responsibilitiesResult = $this->calculateResponsibilitiesScore($job, $resumeExperiences);

            // 4. Calculate Education Score
            $educationResult = $this->calculateEducationScore($jobRequirements, $resumeEducations);

            // 5. Calculate Keywords Score
            $keywordsResult = $this->calculateKeywordsScore($job->description, $resume);

            // 6. Calculate Preferences Score
            $preferencesResult = $this->calculatePreferencesScore($job, $resume->user);

            // 7. Calculate Overall Score
            $overallScore = round(
                ($skillsResult['score'] * self::WEIGHT_SKILLS) +
                ($experienceResult['score'] * self::WEIGHT_EXPERIENCE) +
                ($responsibilitiesResult['score'] * self::WEIGHT_RESPONSIBILITIES) +
                ($educationResult['score'] * self::WEIGHT_EDUCATION) +
                ($keywordsResult['score'] * self::WEIGHT_KEYWORDS) +
                ($preferencesResult['score'] * self::WEIGHT_PREFERENCES),
                2
            );

            // 8. Determine recommendation
            $recommendation = $this->getRecommendation($overallScore);

            // 9. Generate explanation
            $explanation = $this->generateExplanation(
                $overallScore, $skillsResult, $experienceResult,
                $responsibilitiesResult, $educationResult
            );

            // 10. Save or update match
            $jobMatch = JobMatch::updateOrCreate(
                [
                    'user_id' => $resume->user_id,
                    'job_id' => $job->id,
                    'resume_id' => $resume->id,
                ],
                [
                    'overall_score' => $overallScore,
                    'skills_score' => $skillsResult['score'],
                    'experience_score' => $experienceResult['score'],
                    'responsibilities_score' => $responsibilitiesResult['score'],
                    'education_score' => $educationResult['score'],
                    'keywords_score' => $keywordsResult['score'],
                    'preferences_score' => $preferencesResult['score'],
                    'explanation' => $explanation,
                    'recommendation' => $recommendation,
                ]
            );

            // 11. Save match components
            $this->saveComponents($jobMatch, [
                ['Skills', self::WEIGHT_SKILLS, $skillsResult['score'], $skillsResult['details']],
                ['Experience', self::WEIGHT_EXPERIENCE, $experienceResult['score'], $experienceResult['details']],
                ['Responsibilities', self::WEIGHT_RESPONSIBILITIES, $responsibilitiesResult['score'], $responsibilitiesResult['details']],
                ['Education', self::WEIGHT_EDUCATION, $educationResult['score'], $educationResult['details']],
                ['Keywords', self::WEIGHT_KEYWORDS, $keywordsResult['score'], $keywordsResult['details']],
                ['Preferences', self::WEIGHT_PREFERENCES, $preferencesResult['score'], $preferencesResult['details']],
            ]);

            // 12. Save requirement matches
            $this->saveRequirementMatches($jobRequirements, $resumeSkills, $resume);

            DB::commit();

            return [
                'success' => true,
                'data' => $jobMatch->load('components'),
                'message' => 'Match calculated successfully',
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Failed to calculate match: ' . $e->getMessage(),
            ];
        }
    }

    protected function calculateSkillsScore($jobRequirements, array $resumeSkills): array
    {
        $skillRequirements = $jobRequirements->where('requirement_type', 'skill')
            ->where('classification', 'mandatory');

        if ($skillRequirements->isEmpty()) {
            return ['score' => 100, 'details' => ['note' => 'No mandatory skill requirements']];
        }

        $totalScore = 0;
        $details = [];

        foreach ($skillRequirements as $req) {
            $normalizedReq = strtolower(trim($req->normalized_value ?? $req->requirement_text));
            $matchType = $this->matchSkill($normalizedReq, $resumeSkills);
            $matchScore = $this->getMatchScore($matchType);
            $totalScore += $matchScore;

            $details[] = [
                'requirement' => $req->normalized_value ?? $req->requirement_text,
                'match_type' => $matchType,
                'score' => $matchScore,
            ];
        }

        $score = round(($totalScore / $skillRequirements->count()) * 100, 2);

        return ['score' => $score, 'details' => $details];
    }

    public function matchSkill(string $requiredSkill, array $resumeSkills): string
    {
        // Check NOT_EQUIVALENT first (prevent false positives)
        foreach (self::NOT_EQUIVALENT as $skill => $notEquivalent) {
            if ($requiredSkill === $skill) {
                foreach ($notEquivalent as $ne) {
                    if (in_array($ne, $resumeSkills)) {
                        // Don't count this as a match
                        continue;
                    }
                }
            }
        }

        // 1. Exact match
        if (in_array($requiredSkill, $resumeSkills)) {
            return 'exact';
        }

        // 2. Equivalent match
        $equivalent = self::EQUIVALENCIES[$requiredSkill] ?? null;
        if ($equivalent && in_array($equivalent, $resumeSkills)) {
            return 'equivalent';
        }

        // Check reverse equivalencies
        foreach (self::EQUIVALENCIES as $alias => $canonical) {
            if ($canonical === $requiredSkill && in_array($alias, $resumeSkills)) {
                return 'equivalent';
            }
        }

        // 3. Related match (but NOT for mandatory requirements in eligibility)
        $related = self::RELATED_SKILLS[$requiredSkill] ?? [];
        foreach ($related as $relatedSkill) {
            if (in_array($relatedSkill, $resumeSkills)) {
                return 'related';
            }
        }

        // 4. Partial match (check if required skill is a substring of resume skills or vice versa)
        foreach ($resumeSkills as $resumeSkill) {
            if (str_contains($resumeSkill, $requiredSkill) || str_contains($requiredSkill, $resumeSkill)) {
                // But NOT for known non-equivalents
                $isNotEquivalent = false;
                foreach (self::NOT_EQUIVALENT as $skill => $notEquiv) {
                    if (($requiredSkill === $skill && in_array($resumeSkill, $notEquiv)) ||
                        ($resumeSkill === $skill && in_array($requiredSkill, $notEquiv))) {
                        $isNotEquivalent = true;
                        break;
                    }
                }
                if (!$isNotEquivalent) {
                    return 'partial';
                }
            }
        }

        return 'missing';
    }

    protected function getMatchScore(string $matchType): float
    {
        return match ($matchType) {
            'exact' => self::SKILL_EXACT,
            'equivalent' => self::SKILL_EQUIVALENT,
            'related' => self::SKILL_RELATED,
            'partial' => self::SKILL_PARTIAL,
            default => self::SKILL_MISSING,
        };
    }

    protected function calculateExperienceScore($jobRequirements, $resumeExperiences): array
    {
        $expRequirements = $jobRequirements->where('requirement_type', 'experience');

        if ($expRequirements->isEmpty()) {
            return ['score' => 100, 'details' => ['note' => 'No experience requirements']];
        }

        $totalScore = 0;
        $details = [];

        foreach ($expRequirements as $req) {
            $requiredMonths = $this->parseExperienceMonths($req->min_value);
            $actualMonths = $this->calculateTotalExperienceMonths($resumeExperiences);

            if ($requiredMonths === 0) {
                $totalScore += 1.0;
                $details[] = ['requirement' => $req->requirement_text, 'score' => 1.0];
                continue;
            }

            if ($actualMonths >= $requiredMonths) {
                $totalScore += 1.0;
                $details[] = [
                    'requirement' => $req->requirement_text,
                    'required_months' => $requiredMonths,
                    'actual_months' => $actualMonths,
                    'score' => 1.0,
                ];
            } elseif ($actualMonths >= ($requiredMonths * 0.5)) {
                $partialScore = round($actualMonths / $requiredMonths, 2);
                $totalScore += $partialScore;
                $details[] = [
                    'requirement' => $req->requirement_text,
                    'required_months' => $requiredMonths,
                    'actual_months' => $actualMonths,
                    'score' => $partialScore,
                    'note' => 'Partially satisfied',
                ];
            } else {
                $partialScore = $actualMonths > 0 ? round($actualMonths / $requiredMonths, 2) : 0;
                $totalScore += $partialScore;
                $details[] = [
                    'requirement' => $req->requirement_text,
                    'required_months' => $requiredMonths,
                    'actual_months' => $actualMonths,
                    'score' => $partialScore,
                    'note' => 'Not satisfied',
                ];
            }
        }

        $score = round(($totalScore / $expRequirements->count()) * 100, 2);

        return ['score' => $score, 'details' => $details];
    }

    public function parseExperienceMonths(?string $minValue): int
    {
        if (!$minValue) return 0;

        $minValue = strtolower($minValue);

        if (preg_match('/(\d+)\s*year/', $minValue, $matches)) {
            return (int)$matches[1] * 12;
        }

        if (preg_match('/(\d+)\s*month/', $minValue, $matches)) {
            return (int)$matches[1];
        }

        return 0;
    }

    public function calculateTotalExperienceMonths($experiences): int
    {
        $totalMonths = 0;

        foreach ($experiences as $exp) {
            if ($exp->start_date) {
                $endDate = $exp->is_current ? now() : ($exp->end_date ?? now());
                $months = $exp->start_date->diffInMonths($endDate);
                $totalMonths += $months;
            }
        }

        return $totalMonths;
    }

    protected function calculateResponsibilitiesScore(Job $job, $resumeExperiences): array
    {
        $jobDesc = strtolower($job->description);
        $resumeText = '';

        foreach ($resumeExperiences as $exp) {
            $resumeText .= ' ' . strtolower($exp->responsibilities);
        }

        // Key responsibility themes to check
        $themes = [
            'database' => ['database', 'schema', 'table', 'rdbms'],
            'sql' => ['sql', 'query', 'queries', 'mysql'],
            'design' => ['design', 'architect', 'structure', 'normalize'],
            'migration' => ['migration', 'migrate', 'data transfer'],
            'optimization' => ['optim', 'performance', 'speed', 'improve'],
            'qa' => ['qa', 'test', 'testing', 'verification', 'bug'],
            'collaboration' => ['collaborat', 'team', 'work with', 'support'],
        ];

        $matchedThemes = 0;
        $totalThemes = 0;
        $details = [];

        foreach ($themes as $theme => $keywords) {
            $inJob = false;
            foreach ($keywords as $keyword) {
                if (str_contains($jobDesc, $keyword)) {
                    $inJob = true;
                    break;
                }
            }

            if ($inJob) {
                $totalThemes++;
                $inResume = false;
                foreach ($keywords as $keyword) {
                    if (str_contains($resumeText, $keyword)) {
                        $inResume = true;
                        break;
                    }
                }

                if ($inResume) {
                    $matchedThemes++;
                    $details[] = ['theme' => $theme, 'matched' => true];
                } else {
                    $details[] = ['theme' => $theme, 'matched' => false];
                }
            }
        }

        $score = $totalThemes > 0 ? round(($matchedThemes / $totalThemes) * 100, 2) : 100;

        return ['score' => $score, 'details' => $details];
    }

    protected function calculateEducationScore($jobRequirements, $resumeEducations): array
    {
        $eduRequirements = $jobRequirements->where('requirement_type', 'education');

        if ($eduRequirements->isEmpty()) {
            return ['score' => 100, 'details' => ['note' => 'No education requirements']];
        }

        $totalScore = 0;
        $details = [];

        foreach ($eduRequirements as $req) {
            $normalizedReq = strtolower($req->normalized_value ?? $req->requirement_text);
            $matched = false;

            foreach ($resumeEducations as $edu) {
                $degreeMatch = $this->matchEducation($normalizedReq, $edu);
                if ($degreeMatch) {
                    $matched = true;
                    $details[] = [
                        'requirement' => $req->requirement_text,
                        'matched_with' => $edu->degree . ' in ' . $edu->field_of_study,
                        'score' => 1.0,
                    ];
                    break;
                }
            }

            if (!$matched) {
                $details[] = [
                    'requirement' => $req->requirement_text,
                    'matched_with' => null,
                    'score' => 0.0,
                ];
            }

            $totalScore += $matched ? 1.0 : 0.0;
        }

        $score = round(($totalScore / $eduRequirements->count()) * 100, 2);

        return ['score' => $score, 'details' => $details];
    }

    public function matchEducation(string $required, $education): bool
    {
        $degree = strtolower($education->degree);
        $field = strtolower($education->field_of_study);

        // Check degree level
        if (str_contains($required, 'bachelor') && !str_contains($degree, 'bachelor')) {
            return false;
        }

        if (str_contains($required, 'master') && !str_contains($degree, 'master')) {
            return false;
        }

        // Check field of study
        $relatedFields = [
            'computer science' => ['computer science', 'cs', 'information technology', 'it', 'software engineering', 'computer engineering'],
            'information technology' => ['information technology', 'it', 'computer science', 'cs', 'information systems'],
        ];

        foreach ($relatedFields as $key => $aliases) {
            if (str_contains($required, $key)) {
                foreach ($aliases as $alias) {
                    if (str_contains($field, $alias)) {
                        return true;
                    }
                }
                return false;
            }
        }

        // If "or related field" is mentioned, be more lenient
        if (str_contains($required, 'related')) {
            return true;
        }

        return str_contains($field, $required) || str_contains($required, $field);
    }

    protected function calculateKeywordsScore(string $jobDescription, Resume $resume): array
    {
        $jobWords = array_unique(str_word_count(strtolower($jobDescription), 1));
        $resumeText = strtolower($resume->raw_text ?? '');

        // Filter out common words
        $stopWords = ['the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 'is', 'are', 'was', 'were', 'be', 'been', 'being', 'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would', 'could', 'should', 'may', 'might', 'can', 'shall', 'this', 'that', 'these', 'those', 'it', 'its', 'we', 'our', 'you', 'your', 'they', 'their', 'he', 'she', 'his', 'her', 'as', 'from', 'not', 'no', 'all', 'each', 'every', 'both', 'few', 'more', 'most', 'other', 'some', 'such', 'than', 'too', 'very', 'just', 'about', 'up', 'out', 'if', 'then', 'also', 'into', 'through', 'during', 'before', 'after', 'above', 'below', 'between', 'under', 'over', 'again', 'further', 'once', 'here', 'there', 'when', 'where', 'why', 'how', 'what', 'which', 'who', 'whom'];

        $jobKeywords = array_diff($jobWords, $stopWords);
        $jobKeywords = array_filter($jobKeywords, fn($w) => strlen($w) > 3);

        if (empty($jobKeywords)) {
            return ['score' => 100, 'details' => ['note' => 'No significant keywords']];
        }

        $matched = 0;
        foreach ($jobKeywords as $keyword) {
            if (str_contains($resumeText, $keyword)) {
                $matched++;
            }
        }

        $score = round(($matched / count($jobKeywords)) * 100, 2);

        return [
            'score' => min($score, 100),
            'details' => [
                'total_keywords' => count($jobKeywords),
                'matched_keywords' => $matched,
            ],
        ];
    }

    protected function calculatePreferencesScore(Job $job, $user): array
    {
        $preferences = $user->preferences ?? null;

        if (!$preferences) {
            return ['score' => 100, 'details' => ['note' => 'No preferences set, neutral score']];
        }

        // For now, return neutral score since preferences aren't implemented yet
        return ['score' => 100, 'details' => ['note' => 'Preferences matching coming soon']];
    }

    protected function getRecommendation(float $score): string
    {
        if ($score >= 85) return 'high_priority';
        if ($score >= 70) return 'good_match';
        if ($score >= 55) return 'possible_match';
        if ($score >= 40) return 'weak_match';
        return 'not_recommended';
    }

    protected function generateExplanation(
        float $overall, array $skills, array $experience,
        array $responsibilities, array $education
    ): string {
        $lines = [];
        $lines[] = "Overall Match: {$overall}%";
        $lines[] = "";

        if ($skills['score'] >= 80) {
            $lines[] = "✓ Strong skills alignment ({$skills['score']}%)";
        } elseif ($skills['score'] >= 50) {
            $lines[] = "△ Partial skills match ({$skills['score']}%)";
        } else {
            $lines[] = "✗ Significant skill gaps ({$skills['score']}%)";
        }

        if ($experience['score'] >= 80) {
            $lines[] = "✓ Experience requirements met ({$experience['score']}%)";
        } elseif ($experience['score'] >= 50) {
            $lines[] = "△ Experience partially met ({$experience['score']}%)";
        } else {
            $lines[] = "✗ Experience requirements not met ({$experience['score']}%)";
        }

        if ($education['score'] >= 100) {
            $lines[] = "✓ Education requirements satisfied ({$education['score']}%)";
        } elseif ($education['score'] >= 50) {
            $lines[] = "△ Education partially matched ({$education['score']}%)";
        } else {
            $lines[] = "✗ Education requirements not met ({$education['score']}%)";
        }

        $lines[] = "";
        $lines[] = "Responsibilities alignment: {$responsibilities['score']}%";

        return implode("\n", $lines);
    }

    protected function saveComponents(JobMatch $jobMatch, array $components): void
    {
        // Delete old components
        $jobMatch->components()->delete();

        foreach ($components as $component) {
            MatchComponent::create([
                'job_match_id' => $jobMatch->id,
                'component_name' => $component[0],
                'weight' => $component[1],
                'score' => $component[2],
                'details' => $component[3],
            ]);
        }
    }

    protected function saveRequirementMatches($jobRequirements, array $resumeSkills, Resume $resume): void
    {
        foreach ($jobRequirements as $req) {
            if ($req->requirement_type === 'skill') {
                $normalizedReq = strtolower(trim($req->normalized_value ?? $req->requirement_text));
                $matchType = $this->matchSkill($normalizedReq, $resumeSkills);

                JobRequirementMatch::updateOrCreate(
                    [
                        'job_requirement_id' => $req->id,
                        'resume_id' => $resume->id,
                    ],
                    [
                        'match_type' => $matchType,
                        'matched_value' => $matchType !== 'missing' ? $req->normalized_value : null,
                        'match_details' => "Skill match: {$matchType}",
                    ]
                );
            }
        }
    }
}