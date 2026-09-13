<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResumeExperience extends Model
{
    protected $table = 'resume_experiences'; // <-- Add this line

    protected $fillable = [
        'resume_id', 'job_title', 'company', 'start_date', 
        'end_date', 'is_current', 'responsibilities'
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'start_date' => 'date:Y-m',
        'end_date' => 'date:Y-m',
    ];

    protected $appends = ['end_date_or_present'];

    public function getEndDateOrPresentAttribute()
    {
        return $this->is_current ? 'Present' : ($this->end_date ? $this->end_date->format('Y-m') : null);
    }

    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }
}