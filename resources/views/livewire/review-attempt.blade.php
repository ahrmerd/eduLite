<?php

use Livewire\Volt\Component;
use App\Models\QuizAttempt;

new class extends Component {
    public QuizAttempt $quizAttempt;
    public $questions;
    public $userAnswers;
    public $currentQuestionIndex = 0;

    public function mount(QuizAttempt $quizAttempt)
    {
        $this->quizAttempt = $quizAttempt;
        // $this->questions = $quizAttempt->subject->questions;
        $this->userAnswers = $quizAttempt->answers_json;
    }

    public function nextQuestion()
    {
        if ($this->currentQuestionIndex < count($this->questions) - 1) {
            $this->currentQuestionIndex++;
        }
    }

    public function previousQuestion()
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
        }
    }

    public function with(): array
    {
        $questions = $this->questions;
        $currentQuestion = $this->questions[$this->currentQuestionIndex];
        return [
            'questions' => $questions,
            'currentQuestion' => $currentQuestion ,
            'userAnswer' => $this->userAnswers[$currentQuestion->id] ?? null,
        ];
    }
}; ?>

<div class="container p-4 mx-auto">
    <h1 class="mb-4 text-2xl font-bold">Quiz Review: {{ $quizAttempt->subject->name }}</h1>
    <p class="mb-4">Your Score: {{ $quizAttempt->score }} / {{ $quizAttempt->total }}</p>

    <div class="px-8 pt-6 pb-8 mb-4 bg-white rounded shadow-md">
        <h2 class="mb-4 text-xl">Question {{ $currentQuestionIndex + 1 }} of {{ count($questions) }}</h2>
        <p class="mb-4">{{ $currentQuestion->content }}</p>
        
        @foreach ($currentQuestion->options as $index => $option)
            <div class="mb-2">
                <label class="inline-flex items-center">
                    <input type="radio" class="form-radio" disabled 
                           @if($userAnswer == $index) checked @endif>
                    <span class="ml-2 {{ $index == $currentQuestion->correct_answer ? 'text-green-600 font-bold' : '' }}
                                 {{ $userAnswer == $index && $index != $currentQuestion->correct_answer ? 'text-red-600' : '' }}">
                        {{ $option }}
                    </span>
                </label>
            </div>
        @endforeach

        @if ($userAnswer !== null && $userAnswer != $currentQuestion->correct_answer)
            <p class="mt-2 text-red-600">Your answer was incorrect.</p>
        @elseif ($userAnswer !== null)
            <p class="mt-2 text-green-600">Your answer was correct!</p>
        @else
            <p class="mt-2 text-yellow-600">You didn't answer this question.</p>
        @endif
    </div>

    <div class="flex justify-between">
        <button wire:click="previousQuestion" class="px-4 py-2 font-bold text-white bg-gray-500 rounded hover:bg-gray-700" 
                @if($currentQuestionIndex === 0) disabled @endif>
            Previous
        </button>
        <button wire:click="nextQuestion" class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700"
                @if($currentQuestionIndex === count($questions) - 1) disabled @endif>
            Next
        </button>
    </div>

    <div class="mt-8">
       {{--  <h3 class="mb-2 text-lg font-semibold">Question Navigation</h3>
        <div class="flex flex-wrap gap-2">
            @foreach ($questions as $index => $question)
                <button wire:click="$set('currentQuestionIndex', {{ $index }})" 
                        class="w-8 h-8 flex items-center justify-center rounded-full 
                               {{ isset($userAnswers[$question->id]) ? 
                                  ($userAnswers[$question->id] == $question->correct_answer ? 'bg-green-500' : 'bg-red-500') : 
                                  'bg-gray-300' }} text-white">
                    {{ $index + 1 }}
                </button>
            @endforeach
        </div>
        --}}
    </div>
</div>