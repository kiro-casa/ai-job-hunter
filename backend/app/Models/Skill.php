<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $table = 'skills'; // <-- Add this line

    protected $fillable = ['name', 'normalized_name', 'category'];

    public function resumes()
    {
        return $this->belongsToMany(Resume::class, 'resume_skills')
                    ->withPivot('proficiency_level');
    }
}