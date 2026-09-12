<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Job;
use App\Models\Resume;
use App\Models\AutomationSetting;
use App\Models\JobMatch;
use App\Services\EligibilityService;

class SafetyGateService
{
    protected EligibilityService $eligibilityService;

    public function __construct(EligibilityService $eligibilityService)
    {
        $this->eligibilityService = $eligibilityService;
    }

    public function evaluate(Application $application): array
    {
        $checks = [];
        $job = $application->job;
        $resume = $application->resume;
        $user = $application->user;
        $settings = $user->automationSettings;

        // 1. Check if resume is verified
        $checks['resume_verified'] = [
            'passed' => $resume && $resume->status === 'verified',
            'reason' => $resume && $resume->status === 'verified' ? 'Resume is verified' : 'Resume is not verified',
        ];

        // 2. Check if auto-apply is enabled
        $checks['auto_apply_enabled'] = [
            'passed' => $settings && $settings->auto_apply_enabled,
            'reason' => $settings && $settings->auto_apply_enabled ? 'Auto-apply is enabled' : 'Auto-apply is disabled',
        ];

        // 3. Check if confirmation is required
        $checks['confirmation_not_required'] = [
            'passed' => !$settings || !$settings->require_confirmation,
            'reason' => !$settings || !$settings->require_confirmation ? 'No confirmation required' : 'Confirmation is required',
        ];

        // 4. Check eligibility
        $eligibilityResult = $this->eligibilityService->checkEligibility($job, $resume);
        $checks['all_mandatory_satisfied'] = [
            'passed' => $eligibilityResult['eligible'] && $eligibilityResult['decision'] === 'AUTO_APPLY',
            'reason' => $eligibilityResult['eligible'] ? 'All mandatory requirements satisfied' : 'Mandatory requirements missing: ' . implode(', ', $eligibilityResult['missing_required']),
        ];

        // 5. Check confidence threshold
        $checks['confidence_above_threshold'] = [
            'passed' => $eligibilityResult['confidence'] >= ($settings->min_confidence ?? 0.95),
            'reason' => $eligibilityResult['confidence'] >= ($settings->min_confidence ?? 0.95) 
                ? "Confidence {$eligibilityResult['confidence']} meets threshold" 
                : "Confidence {$eligibilityResult['confidence']} below threshold " . ($settings->min_confidence ?? 0.95),
        ];

        // 6. Check match score
        $match = JobMatch::where('user_id', $user->id)
            ->where('job_id', $job->id)
            ->where('resume_id', $resume->id)
            ->first();

        $checks['match_score_above_threshold'] = [
            'passed' => $match && $match->overall_score >= ($settings->min_match_score ?? 80),
            'reason' => $match && $match->overall_score >= ($settings->min_match_score ?? 80)
                ? "Match score {$match->overall_score}% meets threshold"
                : "Match score " . ($match?->overall_score ?? 0) . "% below threshold " . ($settings->min_match_score ?? 80) . "%",
        ];

        // 7. Check for duplicate application
        $existingApplication = Application::where('user_id', $user->id)
            ->where('job_id', $job->id)
            ->where('status', 'applied')
            ->exists();

        $checks['not_duplicate'] = [
            'passed' => !$existingApplication,
            'reason' => !$existingApplication ? 'Not a duplicate application' : 'Already applied to this job',
        ];

        // 8. Check rate limit (simplified for now)
        $todayApplications = Application::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->where('application_method', 'auto')
            ->count();

        $checks['rate_limit_ok'] = [
            'passed' => $todayApplications < ($settings->max_applications_per_day ?? 10),
            'reason' => $todayApplications < ($settings->max_applications_per_day ?? 10)
                ? "Rate limit OK ({$todayApplications}/" . ($settings->max_applications_per_day ?? 10) . " today)"
                : "Daily limit reached ({$todayApplications}/" . ($settings->max_applications_per_day ?? 10) . ")",
        ];

        // 9. Check if automation is supported (for now, always false since we don't have real providers)
        $checks['automation_supported'] = [
            'passed' => false, // Always false for MVP
            'reason' => 'No automation provider available (manual application required)',
        ];

        // Determine overall result
        $allPassed = collect($checks)->every(fn($check) => $check['passed']);

        // Special case: if automation is not supported, it's not a "failure" but rather "manual required"
        $blockedReason = null;
        if (!$allPassed) {
            $failedChecks = collect($checks)->filter(fn($check) => !$check['passed']);
            
            // If only automation_supported failed, it's "manual required" not "blocked"
            if ($failedChecks->count() === 1 && $failedChecks->has('automation_supported')) {
                $blockedReason = 'Manual application required (no automation provider)';
            } else {
                $firstFailure = $failedChecks->first();
                $blockedReason = $firstFailure['reason'];
            }
        }

        return [
            'passed' => $allPassed,
            'checks' => $checks,
            'blocked_reason' => $blockedReason,
            'eligibility_data' => $eligibilityResult,
            'match_data' => $match,
        ];
    }
}