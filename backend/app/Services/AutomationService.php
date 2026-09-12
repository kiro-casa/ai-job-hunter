<?php

namespace App\Services;

use App\Models\Application;
use App\Models\ApplicationLog;
use App\Models\Job;
use App\Models\Resume;
use App\Services\SafetyGateService;
use Illuminate\Support\Facades\DB;

class AutomationService
{
    protected SafetyGateService $safetyGate;

    public function __construct(SafetyGateService $safetyGate)
    {
        $this->safetyGate = $safetyGate;
    }

    public function attemptAutoApply(Application $application): array
    {
        DB::beginTransaction();

        try {
            // 1. Run safety gate
            $safetyResult = $this->safetyGate->evaluate($application);

            // 2. Log the attempt
            $log = ApplicationLog::create([
                'application_id' => $application->id,
                'job_id' => $application->job_id,
                'user_id' => $application->user_id,
                'resume_id' => $application->resume_id,
                'match_score' => $safetyResult['match_data']?->overall_score,
                'eligibility_decision' => $safetyResult['eligibility_data']['decision'],
                'required_count' => $safetyResult['eligibility_data']['required_requirements'],
                'required_met' => $safetyResult['eligibility_data']['required_requirements_met'],
                'missing_required' => $safetyResult['eligibility_data']['missing_required'],
                'preferred_count' => $safetyResult['eligibility_data']['preferred_requirements'],
                'preferred_met' => $safetyResult['eligibility_data']['preferred_requirements_met'],
                'missing_preferred' => $safetyResult['eligibility_data']['missing_preferred'],
                'confidence' => $safetyResult['eligibility_data']['confidence'],
                'automation_provider' => 'manual', // No real provider yet
                'attempt_time' => now(),
                'result' => $safetyResult['passed'] ? 'approved' : 'blocked',
                'failure_reason' => $safetyResult['blocked_reason'],
                'safety_gate_passed' => $safetyResult['passed'],
                'safety_gate_details' => $safetyResult['checks'],
                'status' => $safetyResult['passed'] ? 'approved' : 'blocked',
            ]);

            // 3. Update application status
            if ($safetyResult['passed']) {
                // In a real system, this would submit the application
                // For now, we just mark it as ready
                $application->update([
                    'status' => 'ready_to_apply',
                    'automation_status' => 'approved',
                ]);

                DB::commit();

                return [
                    'success' => true,
                    'data' => [
                        'application' => $application,
                        'log' => $log,
                        'safety_result' => $safetyResult,
                    ],
                    'message' => 'Auto-apply approved. Ready for manual submission.',
                ];
            } else {
                $application->update([
                    'automation_status' => 'blocked',
                ]);

                DB::commit();

                return [
                    'success' => false,
                    'data' => [
                        'application' => $application,
                        'log' => $log,
                        'safety_result' => $safetyResult,
                    ],
                    'message' => 'Auto-apply blocked: ' . $safetyResult['blocked_reason'],
                ];
            }

        } catch (\Exception $e) {
            DB::rollBack();

            // Log the failure
            ApplicationLog::create([
                'application_id' => $application->id,
                'job_id' => $application->job_id,
                'user_id' => $application->user_id,
                'resume_id' => $application->resume_id,
                'attempt_time' => now(),
                'result' => 'failed',
                'failure_reason' => 'System error: ' . $e->getMessage(),
                'safety_gate_passed' => false,
                'status' => 'failed',
            ]);

            return [
                'success' => false,
                'message' => 'Auto-apply failed: ' . $e->getMessage(),
            ];
        }
    }
}