<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationNote extends Model
{
    protected $table = 'application_notes';

    protected $fillable = ['application_id', 'note'];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}