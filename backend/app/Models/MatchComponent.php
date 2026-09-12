<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchComponent extends Model
{
    protected $table = 'match_components';

    protected $fillable = [
        'job_match_id', 'component_name', 'weight', 'score', 'details'
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'score' => 'decimal:2',
        'details' => 'array',
    ];

    public function jobMatch()
    {
        return $this->belongsTo(JobMatch::class);
    }
}