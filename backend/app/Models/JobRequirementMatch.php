<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobRequirementMatch extends Model
{
    protected $table = 'job_requirement_matches';

    protected $fillable = [
        'job_requirement_id', 'resume_id', 'match_type', 
        'matched_value', 'match_details'
    ];

    public function requirement()
    {
        return $this->belongsTo(JobRequirement::class, 'job_requirement_id');
    }

    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }
}