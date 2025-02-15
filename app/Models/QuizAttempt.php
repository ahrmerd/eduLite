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
