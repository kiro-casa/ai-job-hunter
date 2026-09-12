<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Tests\Traits\CreatesTestData;
use App\Services\MatchingService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MatchingServiceTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    protected MatchingService $matchingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->matchingService = app(MatchingService::class);
    }

    public function test_exact_skill_match_returns_perfect_score()
    {
        $testData = $this->createUserWithVerifiedResume([
            'skills' => ['SQL', 'MySQL', 'Database Design'],
        ]);
        $job = $this->createJobWithRequirements($testData['user'], [
            'requirements' => [
                ['text' => 'SQL', 'type' => 'skill', 'classification' => 'mandatory', 'normalized' => 'SQL'],
            ],
        ]);

        $matchType = $this->matchingService->matchSkill('sql', ['sql', 'mysql']);

        $this->assertEquals('exact', $matchType);
    }

    public function test_equivalent_skill_match_is_recognized()
    {
        $matchType = $this->matchingService->matchSkill('postgresql', ['postgres']);

        $this->assertEquals('equivalent', $matchType);
    }

    public function test_java_and_javascript_are_not_equivalent()
    {
        // Critical test: prevent false positive between Java and JavaScript
        $matchType = $this->matchingService->matchSkill('java', ['javascript']);

        $this->assertEquals('missing', $matchType);
    }

    public function test_missing_skill_returns_missing()
    {
        $matchType = $this->matchingService->matchSkill('python', ['sql', 'mysql']);

        $this->assertEquals('missing', $matchType);
    }

    public function test_education_match_works_for_cs_degree()
    {
        $testData = $this->createUserWithVerifiedResume();
        $education = $testData['resume']->educations->first();

        $result = $this->matchingService->matchEducation("bachelor's in computer science", $education);

        $this->assertTrue($result);
    }

    public function test_education_match_fails_for_wrong_field()
    {
        $testData = $this->createUserWithVerifiedResume();
        $education = $testData['resume']->educations->first();

        $result = $this->matchingService->matchEducation("master's in computer science", $education);

        $this->assertFalse($result);
    }

    public function test_experience_months_parsing()
    {
        $this->assertEquals(12, $this->matchingService->parseExperienceMonths('1 year'));
        $this->assertEquals(24, $this->matchingService->parseExperienceMonths('2 years'));
        $this->assertEquals(6, $this->matchingService->parseExperienceMonths('6 months'));
        $this->assertEquals(0, $this->matchingService->parseExperienceMonths(null));
    }

    public function test_full_match_calculation_returns_valid_score()
    {
        $testData = $this->createUserWithVerifiedResume();
        $job = $this->createJobWithRequirements($testData['user']);

        $result = $this->matchingService->calculateMatch($job, $testData['resume']);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('data', $result);
        $this->assertGreaterThanOrEqual(0, $result['data']->overall_score);
        $this->assertLessThanOrEqual(100, $result['data']->overall_score);
    }

    public function test_match_score_reflects_skill_gaps()
    {
        // User with only SQL, job requires SQL + Python
        $testData = $this->createUserWithVerifiedResume(['skills' => ['SQL']]);
        $job = $this->createJobWithRequirements($testData['user'], [
            'requirements' => [
                ['text' => 'SQL', 'type' => 'skill', 'classification' => 'mandatory', 'normalized' => 'SQL'],
                ['text' => 'Python', 'type' => 'skill', 'classification' => 'mandatory', 'normalized' => 'Python'],
            ],
        ]);

        $result = $this->matchingService->calculateMatch($job, $testData['resume']);

        // Should be around 50% since only 1 of 2 mandatory skills is present
        $this->assertTrue($result['success']);
        $this->assertLessThan(70, $result['data']->skills_score);
    }
}