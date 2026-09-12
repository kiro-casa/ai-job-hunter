<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationLog extends Model
{
    protected $table = 'application_logs';

    protected $fillable = [
        'application_id', 'job_id', 'user_id', 'resume_id', 'match_score',
        'eligibility_decision', 'required_count', 'required_met', 'missing_required',
        'preferred_count', 'preferred_met', 'missing_preferred', 'confidence',
        'automation_provider', 'attempt_time', 'result', 'failure_reason',
        'safety_gate_passed', 'safety_gate_details', 'generated_materials', 'status'
    ];

    protected $casts = [
        'match_score' => 'decimal:2',
        'confidence' => 'decimal:4',
        'missing_required' => 'array',
        'missing_preferred' => 'array',
        'safety_gate_details' => 'array',
        'generated_materials' => 'array',
        'attempt_time' => 'datetime',
        'safety_gate_passed' => 'boolean',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}