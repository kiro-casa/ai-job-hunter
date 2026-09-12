<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResumeProfile extends Model
{
    protected $table = 'resume_profiles'; // <-- Add this line

    protected $fillable = [
        'resume_id', 'user_id', 'full_name', 'email', 
        'phone', 'location', 'professional_summary'
    ];

    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }
}