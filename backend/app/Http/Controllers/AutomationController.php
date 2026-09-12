<?php

namespace App\Http\Controllers;

use App\Models\AutomationSetting;
use App\Models\Application;
use App\Services\AutomationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutomationController extends Controller
{
    protected AutomationService $automationService;

    public function __construct(AutomationService $automationService)
    {
        $this->automationService = $automationService;
    }

    public function getSettings()
    {
        $settings = Auth::user()->automationSettings;

        if (!$settings) {
            // Create default settings
            $settings = AutomationSetting::create([
                'user_id' => Auth::id(),
                'auto_apply_enabled' => false,
                'require_confirmation' => true,
                'min_match_score' => 80.00,
                'mandatory_threshold' => 100.00,
                'min_confidence' => 0.95,
                'max_applications_per_day' => 10,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'auto_apply_enabled' => 'boolean',
            'require_confirmation' => 'boolean',
            'min_match_score' => 'numeric|min:0|max:100',
            'mandatory_threshold' => 'numeric|min:0|max:100',
            'min_confidence' => 'numeric|min:0|max:1',
            'max_applications_per_day' => 'integer|min:1|max:100',
        ]);

        $settings = Auth::user()->automationSettings;

        if (!$settings) {
            $settings = AutomationSetting::create(array_merge($validated, [
                'user_id' => Auth::id(),
            ]));
        } else {
            $settings->update($validated);
        }

        return response()->json([
            'success' => true,
            'data' => $settings,
            'message' => 'Settings updated successfully',
        ]);
    }

    public function attemptAutoApply($applicationId)
    {
        $application = Auth::user()->applications()
            ->with(['job', 'resume'])
            ->findOrFail($applicationId);

        $result = $this->automationService->attemptAutoApply($application);

        return response()->json($result, $result['success'] ? 200 : 400);
    }
}