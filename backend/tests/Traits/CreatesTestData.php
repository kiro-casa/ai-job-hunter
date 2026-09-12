<?php

namespace Tests\Traits;

use App\Models\User;
use App\Models\Resume;
use App\Models\ResumeProfile;
use App\Models\ResumeEducation;
use App\Models\ResumeExperience;
use App\Models\Skill;

trait CreatesTestData
{
    protected function createUserWithVerifiedResume(array $overrides = []): array
    {
        $user = User::factory()->create($overrides['user'] ?? []);

        $resume = Resume::create([
            'user_id' => $user->id,
            'file_path' => 'resumes/test.pdf',
            'original_filename' => 'test.pdf',
            'raw_text' => 'Test resume text',
            'status' => 'verified',
        ]);

        ResumeProfile::create([
            'resume_id' => $resume->id,
            'user_id' => $user->id,
            'full_name' => $overrides['profile']['full_name'] ?? 'King Xyro B. Casa',
            'email' => $overrides['profile']['email'] ?? 'test@example.com',
            'phone' => $overrides['profile']['phone'] ?? '(+63) 936-924-1261',
            'location' => $overrides['profile']['location'] ?? 'Cabuyao City, Laguna',
            'professional_summary' => $overrides['profile']['summary'] ?? 'Computer Science graduate with database experience.',
        ]);

        ResumeEducation::create([
            'resume_id' => $resume->id,
            'degree' => $overrides['education']['degree'] ?? 'Bachelor of Science',
            'field_of_study' => $overrides['education']['field'] ?? 'Computer Science',
            'institution' => $overrides['education']['institution'] ?? 'Pamantasan ng Cabuyao',
            'start_date' => '2022-06-01',
            'end_date' => '2026-06-01',
            'is_current' => false,
        ]);

        ResumeExperience::create([
            'resume_id' => $resume->id,
            'job_title' => $overrides['experience']['title'] ?? 'Database Designer & QA Intern',
            'company' => $overrides['experience']['company'] ?? 'Ollopa Corporation',
            'start_date' => '2026-02-01',
            'end_date' => '2026-04-30',
            'is_current' => false,
            'responsibilities' => $overrides['experience']['responsibilities'] ?? 'Led database architecture design, executed QA test cases.',
        ]);

        // Attach skills
        $skills = $overrides['skills'] ?? ['SQL', 'MySQL', 'Database Design', 'Git', 'QA Testing'];
        foreach ($skills as $skillName) {
            $skill = Skill::firstOrCreate(
                ['normalized_name' => strtolower($skillName)],
                ['name' => $skillName, 'category' => 'database']
            );
            $resume->skills()->attach($skill->id, ['proficiency_level' => 'advanced']);
        }

        $resume->load(['profile', 'educations', 'experiences', 'skills']);

        return ['user' => $user, 'resume' => $resume];
    }

    protected function createJobWithRequirements(User $user, array $overrides = []): \App\Models\Job
    {
        $job = \App\Models\Job::create([
            'user_id' => $user->id,
            'title' => $overrides['title'] ?? 'Junior Database Administrator',
            'company' => $overrides['company'] ?? 'TechFlow Solutions',
            'location' => $overrides['location'] ?? 'Makati City',
            'description' => $overrides['description'] ?? 'Database role requiring SQL, MySQL, and 1 year experience.',
            'status' => 'new',
        ]);

        $requirements = $overrides['requirements'] ?? [
            ['text' => 'Bachelor\'s in Computer Science', 'type' => 'education', 'classification' => 'mandatory', 'normalized' => 'Bachelor\'s in Computer Science'],
            ['text' => 'SQL proficiency', 'type' => 'skill', 'classification' => 'mandatory', 'normalized' => 'SQL'],
            ['text' => 'MySQL proficiency', 'type' => 'skill', 'classification' => 'mandatory', 'normalized' => 'MySQL'],
            ['text' => '1 year experience', 'type' => 'experience', 'classification' => 'mandatory', 'normalized' => 'Database Experience', 'min' => '1 year'],
            ['text' => 'Python preferred', 'type' => 'skill', 'classification' => 'preferred', 'normalized' => 'Python'],
        ];

        foreach ($requirements as $req) {
            \App\Models\JobRequirement::create([
                'job_id' => $job->id,
                'requirement_text' => $req['text'],
                'requirement_type' => $req['type'],
                'classification' => $req['classification'],
                'normalized_value' => $req['normalized'],
                'min_value' => $req['min'] ?? null,
            ]);
        }

        return $job->load('requirements');
    }
}