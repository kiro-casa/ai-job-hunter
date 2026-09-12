<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = 'jobs';

    protected $fillable = [
        'user_id', 'title', 'company', 'location', 'work_arrangement',
        'employment_type', 'salary_min', 'salary_max', 'description',
        'responsibilities', 'qualifications', 'source', 'external_url',
        'application_url', 'date_posted', 'date_discovered', 'status',
        'application_method', 'automation_available'
    ];

    protected $casts = [
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'date_posted' => 'date',
        'date_discovered' => 'datetime',
        'automation_available' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function requirements()
    {
        return $this->hasMany(JobRequirement::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'job_skills')
                    ->withPivot('requirement_level');
    }
}