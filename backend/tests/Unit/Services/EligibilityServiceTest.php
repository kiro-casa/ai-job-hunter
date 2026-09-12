<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Tests\Traits\CreatesTestData;
use App\Services\EligibilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EligibilityServiceTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    protected EligibilityService $eligibilityService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->eligibilityService = app(EligibilityService::class);
    }

    public function test_user_with_all_mandatory_requirements_is_eligible()
    {
        $testData = $this->createUserWithVerifiedResume([
            'skills' => ['SQL', 'MySQL', 'Database Design', 'Git'],
        ]);
        
        // Job with only skill requirements that user has
        $job = $this->createJobWithRequirements($testData['user'], [
            'requirements' => [
                ['text' => 'SQL', 'type' => 'skill', 'classification' => 'mandatory', 'normalized' => 'SQL'],
                ['text' => 'MySQL', 'type' => 'skill', 'classification' => 'mandatory', 'normalized' => 'MySQL'],
            ],
        ]);

        $result = $this->eligibilityService->checkEligibility($job, $testData['resume']);

        $this->assertTrue($result['eligible']);
        $this->assertEquals('AUTO_APPLY', $result['decision']);
    }

        public function test_user_missing_mandatory_skill_is_not_eligible()
    {
        $testData = $this->createUserWithVerifiedResume([
            'skills' => ['Python'], // User has Python, but job requires Java
        ]);
        
        $job = $this->createJobWithRequirements($testData['user'], [
            'requirements' => [
                ['text' => 'Python', 'type' => 'skill', 'classification' => 'mandatory', 'normalized' => 'Python'],
                ['text' => 'Java', 'type' => 'skill', 'classification' => 'mandatory', 'normalized' => 'Java'],
            ],
        ]);

        $result = $this->eligibilityService->checkEligibility($job, $testData['resume']);

        $this->assertFalse($result['eligible']);
        $this->assertEquals('DO_NOT_APPLY', $result['decision']);
        $this->assertContains('Java', $result['missing_required']);
    }

    public function test_missing_preferred_skill_does_not_block_eligibility()
    {
        $testData = $this->createUserWithVerifiedResume([
            'skills' => ['SQL', 'MySQL'], // Missing Python (preferred)
        ]);
        
        $job = $this->createJobWithRequirements($testData['user'], [
            'requirements' => [
                ['text' => 'SQL', 'type' => 'skill', 'classification' => 'mandatory', 'normalized' => 'SQL'],
                ['text' => 'Python', 'type' => 'skill', 'classification' => 'preferred', 'normalized' => 'Python'],
            ],
        ]);

        $result = $this->eligibilityService->checkEligibility($job, $testData['resume']);

        $this->assertTrue($result['eligible']);
        $this->assertContains('Python', $result['missing_preferred']);
    }

    public function test_unverified_resume_blocks_eligibility()
    {
        $testData = $this->createUserWithVerifiedResume();
        $testData['resume']->update(['status' => 'extracted']); // Not verified
        
        $job = $this->createJobWithRequirements($testData['user']);

        $result = $this->eligibilityService->checkEligibility($job, $testData['resume']);

        $this->assertFalse($result['eligible']);
        $this->assertEquals('DO_NOT_APPLY', $result['decision']);
    }

    public function test_experience_requirement_is_evaluated()
    {
        $testData = $this->createUserWithVerifiedResume();
        
        // Job requires 1 year, user has ~3 months
        $job = $this->createJobWithRequirements($testData['user'], [
            'requirements' => [
                ['text' => '1 year experience', 'type' => 'experience', 'classification' => 'mandatory', 'normalized' => 'Database Experience', 'min' => '1 year'],
            ],
        ]);

        $result = $this->eligibilityService->checkEligibility($job, $testData['resume']);

        // User has only 3 months, so should not be eligible
        $this->assertFalse($result['eligible']);
    }
}