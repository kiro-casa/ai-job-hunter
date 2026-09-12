<?php

namespace App\Services;

use App\Models\Application;
use App\Models\ApplicationNote;
use App\Models\Job;
use App\Models\Resume;
use Illuminate\Support\Facades\DB;

class ApplicationService
{
        public function createApplication(int $userId, int $jobId, ?int $resumeId = null): array
    {
        try {
            // Use firstOrCreate to prevent duplicate entry errors
            $application = Application::firstOrCreate(
                [
                    'user_id' => $userId,
                    'job_id' => $jobId,
                ],
                [
                    'resume_id' => $resumeId,
                    'status' => 'saved',
                    'application_method' => 'manual',
                    'automation_status' => 'not_applicable',
                ]
            );

            // Check if it was newly created or already existed
            $wasRecentlyCreated = $application->wasRecentlyCreated;

            return [
                'success' => true,
                'data' => $application->load(['job', 'notes']),
                'message' => $wasRecentlyCreated ? 'Application saved successfully' : 'Job is already in your applications',
                'already_exists' => !$wasRecentlyCreated,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create application: ' . $e->getMessage(),
            ];
        }
    }

    public function updateStatus(int $applicationId, string $status): array
    {
        $application = Application::findOrFail($applicationId);
        $application->update(['status' => $status]);

        if ($status === 'applied') {
            $application->update(['applied_at' => now()]);
        }

        return [
            'success' => true,
            'data' => $application->load(['job', 'notes']),
            'message' => 'Application status updated',
        ];
    }

    public function addNote(int $applicationId, string $note): array
    {
        $applicationNote = ApplicationNote::create([
            'application_id' => $applicationId,
            'note' => $note,
        ]);

        return [
            'success' => true,
            'data' => $applicationNote,
            'message' => 'Note added successfully',
        ];
    }

    public function getApplicationsByStatus(int $userId): array
    {
        $applications = Application::where('user_id', $userId)
            ->with(['job', 'notes'])
            ->get()
            ->groupBy('status');

        return [
            'success' => true,
            'data' => $applications,
        ];
    }

    public function getDashboardStats(int $userId): array
    {
        $applications = Application::where('user_id', $userId)->get();

        return [
            'success' => true,
            'data' => [
                'total' => $applications->count(),
                'saved' => $applications->where('status', 'saved')->count(),
                'qualified' => $applications->where('status', 'qualified')->count(),
                'ready_to_apply' => $applications->where('status', 'ready_to_apply')->count(),
                'applied' => $applications->where('status', 'applied')->count(),
                'screening' => $applications->where('status', 'screening')->count(),
                'interview' => $applications->where('status', 'interview')->count(),
                'offer' => $applications->where('status', 'offer')->count(),
                'accepted' => $applications->where('status', 'accepted')->count(),
                'rejected' => $applications->where('status', 'rejected')->count(),
            ],
        ];
    }
}