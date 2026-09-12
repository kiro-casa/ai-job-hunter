<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Tests\Traits\CreatesTestData;
use App\Services\SafetyGateService;
use App\Models\Application;
use App\Models\AutomationSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SafetyGateServiceTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    protected SafetyGateService $safetyGate;

    protected function setUp(): void
    {
        parent::setUp();
        $this->safetyGate = app(SafetyGateService::class);
    }

    public function test_application_without_resume_is_blocked()
    {
        $testData = $this->createUserWithVerifiedResume();
        $job = $this->createJobWithRequirements($testData['user']);
        
        $application = Application::create([
            'user_id' => $testData['user']->id,
            'job_id' => $job->id,
            'resume_id' => null, // No resume
            'status' => 'saved',
        ]);
        $application->load(['job', 'resume', 'user']);

        $result = $this->safetyGate->evaluate($application);

        $this->assertFalse($result['passed']);
        $this->assertStringContainsString('resume', strtolower($result['blocked_reason']));
    }

    public function test_auto_apply_disabled_blocks_application()
    {
        $testData = $this->createUserWithVerifiedResume();
        $job = $this->createJobWithRequirements($testData['user']);
        
        AutomationSetting::create([
            'user_id' => $testData['user']->id,
            'auto_apply_enabled' => false,
            'require_confirmation' => true,
            'min_match_score' => 80,
            'min_confidence' => 0.95,
            'max_applications_per_day' => 10,
        ]);

        $application = Application::create([
            'user_id' => $testData['user']->id,
            'job_id' => $job->id,
            'resume_id' => $testData['resume']->id,
            'status' => 'saved',
        ]);
        $application->load(['job', 'resume', 'user']);

        $result = $this->safetyGate->evaluate($application);

        $this->assertFalse($result['passed']);
        $this->assertFalse($result['checks']['auto_apply_enabled']['passed']);
    }

    public function test_all_checks_are_logged()
    {
        $testData = $this->createUserWithVerifiedResume();
        $job = $this->createJobWithRequirements($testData['user']);
        
        $application = Application::create([
            'user_id' => $testData['user']->id,
            'job_id' => $job->id,
            'resume_id' => $testData['resume']->id,
            'status' => 'saved',
        ]);
        $application->load(['job', 'resume', 'user']);

        $result = $this->safetyGate->evaluate($application);

        // Should have 9 checks
        $this->assertCount(9, $result['checks']);
        
        // Each check should have 'passed' and 'reason'
        foreach ($result['checks'] as $check) {
            $this->assertArrayHasKey('passed', $check);
            $this->assertArrayHasKey('reason', $check);
        }
    }

        public function test_duplicate_application_is_detected()
    {
        $testData = $this->createUserWithVerifiedResume();
        $job = $this->createJobWithRequirements($testData['user']);
        
        // Create a single application that is already 'applied'
        // The safety gate will detect this and block auto-apply
        $application = Application::create([
            'user_id' => $testData['user']->id,
            'job_id' => $job->id,
            'resume_id' => $testData['resume']->id,
            'status' => 'applied',
        ]);
        $application->load(['job', 'resume', 'user']);

        $result = $this->safetyGate->evaluate($application);

        $this->assertFalse($result['checks']['not_duplicate']['passed']);
    }
}