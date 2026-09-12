<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResumeCertification extends Model
{
    protected $table = 'resume_certifications'; // <-- Add this line

    protected $fillable = ['resume_id', 'name', 'issuer', 'issue_date', 'expiry_date'];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }
}