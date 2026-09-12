<?php

namespace App\Http\Controllers;

use App\Services\ResumeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResumeController extends Controller
{
    protected ResumeService $resumeService;

    public function __construct(ResumeService $resumeService)
    {
        $this->resumeService = $resumeService;
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:5120', // Max 5MB
        ]);

        $result = $this->resumeService->processUpload(
            ['file' => $request->file('file')],
            Auth::id()
        );

        if ($result['success']) {
            return response()->json($result, 201);
        }

        return response()->json($result, 500);
    }

    public function index()
    {
        $resumes = Auth::user()->resumes()->with([
            'profile', 'educations', 'experiences', 'skills'
        ])->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $resumes,
        ]);
    }

    public function show($id)
    {
        $resume = Auth::user()->resumes()->with([
            'profile', 'educations', 'experiences', 'projects', 
            'certifications', 'languages', 'skills'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $resume,
        ]);
    }

    public function verify($id)
    {
    $resume = Auth::user()->resumes()->findOrFail($id);
    $resume->update(['status' => 'verified']);

    // Reload the resume with all relationships
    $resume = Auth::user()->resumes()->with([
        'profile', 'educations', 'experiences', 'projects', 
        'certifications', 'languages', 'skills'
        ])->findOrFail($id);

    return response()->json([
        'success' => true,
        'data' => $resume,
        'message' => 'Resume verified successfully',
        ]);
    }
}