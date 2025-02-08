<?php

use Livewire\Volt\Component;
use App\Models\QuizAttempt;
use App\Models\Question;
use Illuminate\Support\Facades\DB;

new class extends Component
{
    public QuizAttempt $quizAttempt;
    public $currentQuestionIndex = 0;
    public $selectedAnswer = null;
    public $timeRemaining;
    public $questions;

    public function mount(QuizAttempt $quizAttempt)
    {
        $this->quizAttempt = $quizAttempt;
        $this->questions = $quizAttempt->subject->questions;
        $this->timeRemaining = $this->calculateTimeRemaining();
    }

    public function nextQuestion()
    {
        $this->currentQuestionIndex++;
        $this->selectedAnswer = null;
    }

    public function previousQuestion()
    {
        $this->currentQuestionIndex--;
        $this->selectedAnswer = null;
    }

    public function saveAnswer()
    {
        if ($this->selectedAnswer !== null) {
            $this->checkTimeLimit();
            $currentQuestion = $this->questions[$this->currentQuestionIndex];
            $answers = $this->quizAttempt->answers_json ?? [];
            $answers[$currentQuestion->id] = $this->selectedAnswer;
            $this->quizAttempt->update(['answers_json' => $answers]);
        }
    }

    public function completeQuiz()
    {
        $this->saveAnswer();
        $this->quizAttempt->update(['status' => 'completed']);
        $this->calculateScore();
        $this->redirect(route('quiz-attempts.show', $this->quizAttempt));
    }

    public function checkTimeLimit()
    {
        $this->timeRemaining = $this->calculateTimeRemaining();
        if ($this->timeRemaining <= 0) {
            $this->completeQuiz();
        }
    }

    private function calculateTimeRemaining()
    {
        $startTime = $this->quizAttempt->started_at;
        $timeLimit = $this->quizAttempt->subject->loadCount('questions')->calculateTimeLimit() * 60; // Convert to seconds
        $elapsedTime = $startTime->diffInSeconds(now());
        // $elapsedTime = now()->diffInSeconds($startTime);
        // dump($elapsedTime);
        return max(0, $timeLimit - $elapsedTime);
    }

    private function calculateScore()
    {
        $score = 0;
        $answers = $this->quizAttempt->answers_json;
        foreach ($this->questions as $question) {
            if (isset($answers[$question->id]) && $answers[$question->id] == $question->correct_answer) {
                $score++;
            }
        }
        $this->quizAttempt->update(['score' => $score]);
    }

    public function with(): array
    {
        return [
            'questions' => $this->questions,
        ];
    }
}; ?>

<div>
    <h1 class="mb-4 text-2xl font-bold">{{ $quizAttempt->subject->name }}</h1>

    @if ($quizAttempt->status === 'completed')
    <div class="p-4 mb-4 text-yellow-700 bg-yellow-100 border-l-4 border-yellow-500" role="alert">
        <p>This quiz attempt has been completed.</p>
        <x-primary-button>
            <a href="{{ route('quiz-attempts.review', $quizAttempt) }}">
                Review
            </a>

        </x-primary-button>
    </div>
    <p>Your score: {{ $quizAttempt->score }} / {{ $questions->count() }}</p>
    @else
    <div x-data="{ timeRemaining: $wire.timeRemaining }" x-init="setInterval(() => { 
                 if(timeRemaining > 0) { 
                     timeRemaining--; 
                     $wire.checkTimeLimit();
                 } 
             }, 1000)">
        <p class="mb-4 text-lg">
            Time Remaining:
            <span x-text="`${Math.floor(timeRemaining / 60).toString().padStart(2, '0')}:${Math.floor(timeRemaining % 60).toString().padStart(2, '0')}`"></span>
        </p>

        <!-- <p class="mb-4 text-lg">Time Remaining: <span x-text="Math.floor(timeRemaining / 60).toString().padStart(2, '0') + ':' + (timeRemaining % 60).toString().padStart(2, '0')"></span></p> -->
    </div>

    @if (isset($questions[$currentQuestionIndex]))
    <div class="px-8 pt-6 pb-8 mb-4 bg-white rounded shadow-md">
        <h2 class="mb-4 text-xl">Question {{ $currentQuestionIndex + 1 }} of {{ count($questions) }}</h2>
        <p class="mb-4">{{ $questions[$currentQuestionIndex]->content }}</p>

        @foreach ($questions[$currentQuestionIndex]->options as $index => $option)
        <div class="mb-2">
            <label class="inline-flex items-center">
                <input type="radio" class="form-radio" wire:model="selectedAnswer" value="{{ $index }}" wire:change="saveAnswer">
                <span class="ml-2">{{ $option }}</span>
            </label>
        </div>
        @endforeach
    </div>

    <div class="flex justify-between">
        <button wire:click="previousQuestion" class="px-4 py-2 font-bold text-white bg-gray-500 rounded hover:bg-gray-700" @if($currentQuestionIndex===0) disabled @endif>
            Previous
        </button>
        @if ($currentQuestionIndex < count($questions) - 1) <button wire:click="nextQuestion" class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
            Next
            </button>
            @else
            <button wire:click="completeQuiz" class="px-4 py-2 font-bold text-white bg-green-500 rounded hover:bg-green-700">
                Submit Quiz
            </button>
            @endif
    </div>
    @endif

    <div class="mt-8">
        <h3 class="mb-2 text-lg font-semibold">Question Progress</h3>
        <div class="flex flex-wrap gap-2">
            @foreach ($questions as $index => $question)
            <div class="w-8 h-8 flex items-center justify-center rounded-full {{ isset($quizAttempt->answers_json[$question->id]) ? 'bg-green-500 text-white' : 'bg-gray-300' }}">
                {{ $index + 1 }}
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>