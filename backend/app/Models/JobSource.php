<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobSource extends Model
{
    protected $table = 'job_sources';

    protected $fillable = ['name', 'base_url', 'api_type', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}