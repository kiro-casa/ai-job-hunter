<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resume extends Model
{
    protected $table = 'resumes'; // <-- Add this line

    protected $fillable = [
        'user_id', 'file_path', 'original_filename', 'raw_text', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function profile()
    {
        return $this->hasOne(ResumeProfile::class);
    }

    public function educations()
    {
        return $this->hasMany(ResumeEducation::class);
    }

    public function experiences()
    {
        return $this->hasMany(ResumeExperience::class);
    }

    public function projects()
    {
        return $this->hasMany(ResumeProject::class);
    }

    public function certifications()
    {
        return $this->hasMany(ResumeCertification::class);
    }

    public function languages()
    {
        return $this->hasMany(ResumeLanguage::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'resume_skills')
                    ->withPivot('proficiency_level');
    }
}