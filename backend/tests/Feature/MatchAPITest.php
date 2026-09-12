<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Traits\CreatesTestData;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MatchApiTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    public function test_unauthenticated_user_cannot_access_match_endpoint()
    {
        $response = $this->postJson('/api/jobs/1/match');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_calculate_match()
    {
        $testData = $this->createUserWithVerifiedResume();
        $job = $this->createJobWithRequirements($testData['user']);

        $response = $this->actingAs($testData['user'])
            ->postJson("/api/jobs/{$job->id}/match");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'overall_score',
                    'skills_score',
                    'experience_score',
                    'recommendation',
                ],
            ]);
    }

    public function test_match_endpoint_requires_verified_resume()
    {
        $testData = $this->createUserWithVerifiedResume();
        $testData['resume']->update(['status' => 'extracted']);
        $job = $this->createJobWithRequirements($testData['user']);

        $response = $this->actingAs($testData['user'])
            ->postJson("/api/jobs/{$job->id}/match");

        $response->assertStatus(400);
    }
}