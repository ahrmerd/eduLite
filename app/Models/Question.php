<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $casts = [
        'options' => 'json',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
