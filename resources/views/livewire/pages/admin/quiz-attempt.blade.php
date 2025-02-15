<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\Subject;
use App\Models\Question;
use App\Models\QuizAttempt;
use Illuminate\Support\Facades\DB;

new #[Layout('components.layouts.admin')] class extends Component
// new  class extends Component
{
    public QuizAttempt $quizAttempt;
    public $questions;

    public function mount(QuizAttempt $quizAttempt)
    {
        $this->quizAttempt = $quizAttempt;
        $this->questions = Question::findMany($quizAttempt->selected_question_ids);

    }
}
?>

<div class="p-6 text-gray-900 dark:text-gray-100">
    <livewire:review-attempt :questions="$questions" :quizAttempt="$quizAttempt" />
    <form action="{{ route('quiz-attempts.store', $quizAttempt->subject->id) }}" method="post">
        @csrf
        <input type="text" hidden name="subject_id" value="{{$quizAttempt->subject->id}}" id="">
        <button type="submit" class="btn bg-red-500 text-white hover:underline">Attempt Quiz</button>
    </form>
</div>