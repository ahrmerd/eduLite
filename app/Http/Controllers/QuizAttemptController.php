<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuizAttemptRequest;
use App\Http\Requests\UpdateQuizAttemptRequest;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Subject;
use Illuminate\Http\Request;

class QuizAttemptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $quizAttempts =  auth()->user()->quizAttempts()->with(['quiz' => function ($query) {
            $query->withCount('questions');
        }])->paginate();
        return view('quiz-attempts.index', ['quizAttempts' => $quizAttempts]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // $quiz = Quiz::findOrFail($request->quiz_id);

        // $attempt = QuizAttempt::create([
        //     'user_id' => auth()->id(),
        //     'quiz_id' => $quiz->id,
        //     'created_at' => now(),
        //     'status' => 'progress'
        // ]);

        // return redirect()->route('quiz-attempts.show', $attempt);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreQuizAttemptRequest $request)
    {
        $subject = Subject::findOrFail($request->subject_id);
        $selectedQuestionIds = $subject->questions()
            ->inRandomOrder()
            ->take($subject->questions_per_quiz)
            ->pluck('id')
            ->toArray();
            // $total = Question::whereIn('id', $selected_question_ids)->count();
            // $total = Question::whereIn('id', $selected_question_ids)->toRawSql();
        // dd($selectedQuestionIds);


        $attempt = QuizAttempt::create([
            'user_id' => auth()->id(),
            'selected_question_ids' => $selectedQuestionIds,
            'subject_id' => $subject->id,
            'status' => 'progress'
        ]);
        return redirect(route('quiz-attempts.show', $attempt));
    }

    /**
     * Display the specified resource.
     */
    public function show(QuizAttempt $quizAttempt)
    {
        $this->checkTimeLimit($quizAttempt);

        $subject = $quizAttempt->subject;

        $quizAttempt = $quizAttempt->refresh();

        $questions = Question::findMany($quizAttempt->selected_question_ids);

        if ($quizAttempt->status === 'completed') {
            return view('quiz-attempts.completed', compact('quizAttempt', 'subject'));
        }

        return view('quiz-attempts.show', compact('quizAttempt', 'subject', 'questions'));
        // return view('quizzes-attempt.show');
    }

    private function checkTimeLimit(QuizAttempt $quizAttempt)
    {
        $startTime = $quizAttempt->created_at;

        $timeLimit = $quizAttempt->subject->loadCount('questions')->calculateTimeLimit() * 60; // Convert to seconds
        // $elapsedTime = now()->diffInSeconds($quizAttempt->created_at);
        $elapsedTime = $startTime->diffInSeconds(now());

        if ($elapsedTime > $timeLimit && $quizAttempt->status !== 'completed') {
            $quizAttempt->update(['status' => 'completed']);
            $this->calculateScore($quizAttempt);
        }
    }

    public function review(QuizAttempt $quizAttempt)
    {

        if ($quizAttempt->status !== 'completed') {
            // $quizAttempt->update(['status' => 'completed']);
            // $this->calculateScore($quizAttempt);
            return redirect(route('quiz-attempts.show'));
        }
        $questions = Question::findMany($quizAttempt->selected_question_ids);

        return view('quiz-attempts.review', compact('quizAttempt', 'questions'));

        // return redirect()->route('quiz-attempts.show', $quizAttempt);
    }


    public function complete(QuizAttempt $quizAttempt)
    {
        $this->checkTimeLimit($quizAttempt);

        if ($quizAttempt->status !== 'completed') {
            $quizAttempt->update(['status' => 'completed']);
            $this->calculateScore($quizAttempt);
        }

        return redirect()->route('quiz-attempts.show', $quizAttempt);
    }

    private function calculateScore(QuizAttempt $quizAttempt)
    {
        $score = 0;
        $total = Question::whereIn('id', $quizAttempt->selected_question_ids)->count();

        $answers = $quizAttempt->answers_json;
        foreach ($quizAttempt->subject->questions as $question) {
            if (isset($answers[$question->id]) && $answers[$question->id] == $question->correct_answer) {
                $score++;
            }
        }
        $quizAttempt->update(['score' => $score,  'total'=> $total]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(QuizAttempt $quizAttempt)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQuizAttemptRequest $request, QuizAttempt $quizAttempt)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(QuizAttempt $quizAttempt)
    {
        //
    }
}
