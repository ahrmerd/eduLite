<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuizRequest;
use App\Http\Requests\UpdateQuizRequest;
use App\Models\Quiz;
use Illuminate\Support\Facades\Gate;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $quizzes = Quiz::with('questions')->get();
        return view('quizzes.index', compact('quizzes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('quizzes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreQuizRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Quiz $quiz)
    {
        return view('quizzes.show', compact('quiz'));
    }

    // public function attempt(Quiz $quiz)
    // {
    //     $attempts = $quiz->attempts()->where('')->get();

    //     return view('quizzes.attempt', compact('quiz', 'attempts'));
    // }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quiz $quiz)
    {
        Gate::authorize('update', $quiz);
        return view('quizzes.create', ['quiz'=> $quiz]);
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQuizRequest $request, Quiz $quiz)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quiz $quiz)
    {
        Gate::authorize('delete', $quiz);
        $quiz->delete();
        return redirect(route('quizzes.index'));
    }
}
