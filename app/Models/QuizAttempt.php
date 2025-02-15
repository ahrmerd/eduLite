<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class QuizAttempt extends Model
{
    protected $casts = [
        // 'started_at' => 'datetime',
        'answers_json' => 'json',
        'selected_question_ids' => 'array',
    ];


    protected static function boot()
    {
        parent::boot();

        // Hook into the updating event
        static::updating(function ($model) {
            // getDirty() returns the attributes that have been changed
            $dirty = $model->getDirty();
            Log::info('Updating model with changes: ' . json_encode($dirty));
        });
    }

   
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getScore(){
        if($this->total<1) return 0;
        return  $this->score / $this->total * 100;
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
