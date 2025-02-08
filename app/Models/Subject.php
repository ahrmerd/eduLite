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

    public function calculateTimeLimit(){
        return $this->questions_count * 2;
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
