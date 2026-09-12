<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResumeLanguage extends Model
{
    protected $table = 'resume_languages'; // <-- Add this line

    protected $fillable = ['resume_id', 'language', 'proficiency'];

    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }
}