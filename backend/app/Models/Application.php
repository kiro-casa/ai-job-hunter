<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $table = 'applications';

    protected $fillable = [
        'user_id', 'job_id', 'resume_id', 'status', 'application_method',
        'automation_status', 'applied_at', 'interview_date', 'follow_up_date', 'contact_info'
    ];

    protected $casts = [
        'applied_at' => 'datetime',
        'interview_date' => 'datetime',
        'follow_up_date' => 'date',
        'contact_info' => 'array',
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

    public function notes()
    {
        return $this->hasMany(ApplicationNote::class);
    }

    public function logs()
    {
        return $this->hasMany(ApplicationLog::class);
    }
}