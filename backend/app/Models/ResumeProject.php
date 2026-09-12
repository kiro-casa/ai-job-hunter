<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResumeProject extends Model
{
    protected $table = 'resume_projects'; // <-- Add this line

    protected $fillable = ['resume_id', 'name', 'description', 'technologies'];

    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }
}