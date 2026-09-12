<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobMatch extends Model
{
    protected $table = 'job_matches';

    protected $fillable = [
        'user_id', 'job_id', 'resume_id', 'overall_score',
        'skills_score', 'experience_score', 'responsibilities_score',
        'education_score', 'keywords_score', 'preferences_score',
        'explanation', 'recommendation'
    ];

    protected $casts = [
        'overall_score' => 'decimal:2',
        'skills_score' => 'decimal:2',
        'experience_score' => 'decimal:2',
        'responsibilities_score' => 'decimal:2',
        'education_score' => 'decimal:2',
        'keywords_score' => 'decimal:2',
        'preferences_score' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }

    public function components()
    {
        return $this->hasMany(MatchComponent::class);
    }
}