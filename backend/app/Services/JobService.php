<?php

namespace App\Services;

use App\Models\Job;
use App\Models\JobRequirement;
use App\Models\Skill;
use App\Services\AIService;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;

class JobService
{
    protected AIService $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function createJob(array $jobData, int $userId): array
    {
        DB::beginTransaction();

        try {
            // 1. Create the job record
            $job = Job::create([
                'user_id' => $userId,
                'title' => $jobData['title'],
                'company' => $jobData['company'],
                'location' => $jobData['location'] ?? null,
                'work_arrangement' => $jobData['work_arrangement'] ?? 'unspecified',
                'employment_type' => $jobData['employment_type'] ?? 'unspecified',
                'salary_min' => $jobData['salary_min'] ?? null,
                'salary_max' => $jobData['salary_max'] ?? null,
                'description' => $jobData['description'],
                'source' => $jobData['source'] ?? 'manual',
                'external_url' => $jobData['external_url'] ?? null,
                'application_url' => $jobData['application_url'] ?? null,
                'status' => 'new',
            ]);

            // 2. Extract requirements using AI
            $extractedData = $this->aiService->extractJobRequirements($jobData['description']);

            // 3. Save requirements
            $this->saveRequirements($job, $extractedData);

            DB::commit();

            return [
                'success' => true,
                'data' => $job->load(['requirements', 'skills']),
                'message' => 'Job created and analyzed successfully',
            ];

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Job creation failed: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to create job: ' . $e->getMessage(),
            ];
        }
    }

    protected function saveRequirements(Job $job, array $data): void
    {
        if (!isset($data['requirements'])) {
            return;
        }

        foreach ($data['requirements'] as $reqData) {
            // Create the requirement
            $requirement = JobRequirement::create([
                'job_id' => $job->id,
                'requirement_text' => $reqData['requirement_text'],
                'requirement_type' => $reqData['requirement_type'],
                'classification' => $reqData['classification'],
                'normalized_value' => $reqData['normalized_value'] ?? null,
                'min_value' => $reqData['min_value'] ?? null,
            ]);

            // If it's a skill requirement, link it to the skills table
            if ($reqData['requirement_type'] === 'skill' && isset($reqData['normalized_value'])) {
                $skill = Skill::firstOrCreate(
                    ['normalized_name' => strtolower(trim($reqData['normalized_value']))],
                    [
                        'name' => $reqData['normalized_value'],
                        'category' => 'skill',
                    ]
                );

                $job->skills()->attach($skill->id, [
                    'requirement_level' => $reqData['classification'] === 'mandatory' ? 'required' : $reqData['classification'],
                ]);
            }
        }
    }
}