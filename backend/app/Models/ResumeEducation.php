<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResumeEducation extends Model
{
    protected $table = 'resume_educations'; // <-- Add this line

    protected $fillable = [
        'resume_id', 'degree', 'field_of_study', 'institution', 
        'start_date', 'end_date', 'is_current'
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }
}