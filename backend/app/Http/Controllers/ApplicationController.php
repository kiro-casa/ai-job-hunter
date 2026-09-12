<?php

namespace App\Http\Controllers;

use App\Services\ApplicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    protected ApplicationService $applicationService;

    public function __construct(ApplicationService $applicationService)
    {
        $this->applicationService = $applicationService;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_id' => 'required|exists:jobs,id',
            'resume_id' => 'nullable|exists:resumes,id',
        ]);

        $result = $this->applicationService->createApplication(
            Auth::id(),
            $validated['job_id'],
            $validated['resume_id'] ?? null
        );

        return response()->json($result, $result['success'] ? 201 : 500);
    }

    public function index()
    {
        $result = $this->applicationService->getApplicationsByStatus(Auth::id());
        return response()->json($result);
    }

    public function show($id)
    {
        $application = Auth::user()->applications()
            ->with(['job', 'notes', 'logs'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $application,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:saved,qualified,ready_to_apply,applied,screening,interview,offer,accepted,rejected,withdrawn',
        ]);

        $result = $this->applicationService->updateStatus($id, $validated['status']);
        return response()->json($result);
    }

    public function addNote(Request $request, $id)
    {
        $validated = $request->validate([
            'note' => 'required|string',
        ]);

        $result = $this->applicationService->addNote($id, $validated['note']);
        return response()->json($result, 201);
    }

    public function dashboard()
    {
        $result = $this->applicationService->getDashboardStats(Auth::id());
        return response()->json($result);
    }
}