<?php

namespace App\Services;

use App\Models\Job;
use App\Models\Resume;
use App\Models\JobRequirement;
use App\Models\JobRequirementMatch;
use Illuminate\Support\Facades\DB;

class EligibilityService
{
    protected MatchingService $matchingService;

    public function __construct(MatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    }

    public function checkEligibility(Job $job, Resume $resume): array
    {
        // 1. Ensure resume is verified
        if ($resume->status !== 'verified') {
            return $this->formatResult(false, 'DO_NOT_APPLY', 'Resume is not verified.');
        }

        // 2. Get all mandatory requirements
        $mandatoryRequirements = $job->requirements->where('classification', 'mandatory');
        $preferredRequirements = $job->requirements->where('classification', 'preferred');

        if ($mandatoryRequirements->isEmpty()) {
            return $this->formatResult(true, 'AUTO_APPLY', 'No mandatory requirements found.');
        }

        // 3. Evaluate each mandatory requirement
        $missingRequired = [];
        $uncertainRequired = [];
        $metRequiredCount = 0;

        foreach ($mandatoryRequirements as $req) {
            $match = $this->evaluateRequirement($req, $resume);
            
            if ($match['match_type'] === 'missing' || $match['match_type'] === 'partial') {
                $missingRequired[] = $req->normalized_value ?? $req->requirement_text;
            } elseif ($match['match_type'] === 'uncertain') {
                $uncertainRequired[] = $req->normalized_value ?? $req->requirement_text;
            } else {
                $metRequiredCount++;
            }

            // Save or update the requirement match record
            JobRequirementMatch::updateOrCreate(
                [
                    'job_requirement_id' => $req->id,
                    'resume_id' => $resume->id,
                ],
                [
                    'match_type' => $match['match_type'],
                    'matched_value' => $match['matched_value'],
                    'match_details' => $match['match_details'],
                ]
            );
        }

        // 4. Evaluate preferred requirements (for informational purposes, doesn't block)
        $missingPreferred = [];
        $metPreferredCount = 0;

        foreach ($preferredRequirements as $req) {
            $match = $this->evaluateRequirement($req, $resume);
            if ($match['match_type'] === 'missing' || $match['match_type'] === 'uncertain') {
                $missingPreferred[] = $req->normalized_value ?? $req->requirement_text;
            } else {
                $metPreferredCount++;
            }
        }

        // 5. Determine final decision
        $totalMandatory = $mandatoryRequirements->count();
        
        if (count($missingRequired) > 0) {
            return $this->formatResult(
                false, 
                'DO_NOT_APPLY', 
                'Mandatory requirements missing.', 
                $totalMandatory, $metRequiredCount, count($missingRequired), $missingRequired,
                count($preferredRequirements), $metPreferredCount, $missingPreferred
            );
        }

        if (count($uncertainRequired) > 0) {
            return $this->formatResult(
                false, 
                'REVIEW_REQUIRED', 
                'Some mandatory requirements are uncertain and require manual review.', 
                $totalMandatory, $metRequiredCount, 0, [],
                count($preferredRequirements), $metPreferredCount, $missingPreferred,
                0.85 // Lower confidence due to uncertainty
            );
        }

        // All mandatory requirements met
        return $this->formatResult(
            true, 
            'AUTO_APPLY', 
            'All mandatory requirements satisfied.', 
            $totalMandatory, $metRequiredCount, 0, [],
            count($preferredRequirements), $metPreferredCount, $missingPreferred,
            0.98 // High confidence
        );
    }

    protected function evaluateRequirement(JobRequirement $req, Resume $resume): array
    {
        if ($req->requirement_type === 'skill') {
            $normalizedReq = strtolower(trim($req->normalized_value ?? $req->requirement_text));
            $resumeSkills = $resume->skills->pluck('normalized_name')->map(fn($s) => strtolower($s))->toArray();
            
            $matchType = $this->matchingService->matchSkill($normalizedReq, $resumeSkills);
            
            return [
                'match_type' => $matchType,
                'matched_value' => $matchType !== 'missing' ? $req->normalized_value : null,
                'match_details' => "Skill match type: {$matchType}",
            ];
        }

        if ($req->requirement_type === 'education') {
            $normalizedReq = strtolower($req->normalized_value ?? $req->requirement_text);
            foreach ($resume->educations as $edu) {
                if ($this->matchingService->matchEducation($normalizedReq, $edu)) {
                    return [
                        'match_type' => 'exact',
                        'matched_value' => $edu->degree . ' in ' . $edu->field_of_study,
                        'match_details' => 'Education requirement satisfied',
                    ];
                }
            }
            return [
                'match_type' => 'missing',
                'matched_value' => null,
                'match_details' => 'No matching education found',
            ];
        }

        if ($req->requirement_type === 'experience') {
            $requiredMonths = $this->matchingService->parseExperienceMonths($req->min_value);
            $actualMonths = $this->matchingService->calculateTotalExperienceMonths($resume->experiences);

            if ($actualMonths >= $requiredMonths) {
                return [
                    'match_type' => 'exact',
                    'matched_value' => "{$actualMonths} months",
                    'match_details' => "Required: {$requiredMonths} months, Actual: {$actualMonths} months",
                ];
            } elseif ($actualMonths > 0) {
                return [
                    'match_type' => 'partial', // For eligibility, partial on mandatory = missing/block
                    'matched_value' => "{$actualMonths} months",
                    'match_details' => "Required: {$requiredMonths} months, Actual: {$actualMonths} months",
                ];
            } else {
                return [
                    'match_type' => 'uncertain', // No experience dates found
                    'matched_value' => null,
                    'match_details' => 'Experience duration could not be determined',
                ];
            }
        }

        // Default fallback for other types (certifications, etc.)
        return [
            'match_type' => 'uncertain',
            'matched_value' => null,
            'match_details' => 'Requirement type not fully evaluated yet',
        ];
    }

    protected function formatResult(
        bool $eligible,
        string $decision,
        string $reason,
        int $reqTotal = 0,
        int $reqMet = 0,
        int $reqMissingCount = 0,
        array $missingRequired = [],
        int $prefTotal = 0,
        int $prefMet = 0,
        array $missingPreferred = [],
        float $confidence = 0.95
    ): array {
        return [
            'eligible' => $eligible,
            'decision' => $decision,
            'reason' => $reason,
            'required_requirements' => $reqTotal,
            'required_requirements_met' => $reqMet,
            'required_requirements_missing' => $reqMissingCount,
            'preferred_requirements' => $prefTotal,
            'preferred_requirements_met' => $prefMet,
            'missing_required' => $missingRequired,
            'missing_preferred' => $missingPreferred,
            'confidence' => $confidence,
        ];
    }
}