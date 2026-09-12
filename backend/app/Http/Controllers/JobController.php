<?php

namespace App\Http\Controllers;

use App\Services\JobService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    protected JobService $jobService;

    public function __construct(JobService $jobService)
    {
        $this->jobService = $jobService;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'nullable|string|max:255',
            'work_arrangement' => 'nullable|in:remote,hybrid,on_site,unspecified',
            'employment_type' => 'nullable|in:full_time,part_time,contract,internship,unspecified',
            'salary_min' => 'nullable|numeric',
            'salary_max' => 'nullable|numeric',
            'source' => 'nullable|string|max:255',
            'external_url' => 'nullable|url|max:1000',
            'application_url' => 'nullable|url|max:1000',
        ]);

        $result = $this->jobService->createJob($validated, Auth::id());

        if ($result['success']) {
            return response()->json($result, 201);
        }

        return response()->json($result, 500);
    }

    public function index()
    {
        $jobs = Auth::user()->jobs()
            ->with(['requirements', 'skills'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $jobs,
        ]);
    }

    public function show($id)
    {
        $job = Auth::user()->jobs()
            ->with(['requirements', 'skills'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $job,
        ]);
    }
}