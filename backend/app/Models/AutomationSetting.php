<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutomationSetting extends Model
{
    protected $table = 'automation_settings';

    protected $fillable = [
        'user_id', 'auto_apply_enabled', 'require_confirmation',
        'min_match_score', 'mandatory_threshold', 'min_confidence',
        'max_applications_per_day', 'allowed_job_types', 'allowed_locations',
        'work_arrangement_prefs', 'allowed_employment_types',
        'auto_save_record', 'auto_generate_cover_letter',
        'auto_attach_resume', 'auto_prepare_answers'
    ];

    protected $casts = [
        'auto_apply_enabled' => 'boolean',
        'require_confirmation' => 'boolean',
        'min_match_score' => 'decimal:2',
        'mandatory_threshold' => 'decimal:2',
        'min_confidence' => 'decimal:4',
        'allowed_job_types' => 'array',
        'allowed_locations' => 'array',
        'work_arrangement_prefs' => 'array',
        'allowed_employment_types' => 'array',
        'auto_save_record' => 'boolean',
        'auto_generate_cover_letter' => 'boolean',
        'auto_attach_resume' => 'boolean',
        'auto_prepare_answers' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}