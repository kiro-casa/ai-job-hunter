<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobRequirement extends Model
{
    protected $table = 'job_requirements';

    protected $fillable = [
        'job_id', 'requirement_text', 'requirement_type', 
        'classification', 'normalized_value', 'min_value'
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}