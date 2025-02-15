<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    // public function getTotalQuestionsAttribute()
    // {
    //     return $this->questions()->count();
    // }


    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function calculateTimeLimit() {
        if ($this->questions_count < $this->questions_per_quiz) {
            return $this->questions_count * $this->mins_per_question;
        }
        return $this->questions_per_quiz * $this->mins_per_question;
    }

    public function tutorials()
    {
        return $this->hasMany(Tutorial::class);
    }

    public function pastQuestionMaterials()
    {
        return $this->hasMany(PastQuestionMaterial::class);
    }
}
