<?php

use App\Http\Controllers\QuizAttemptController;
use App\Http\Middleware\CheckRole;
use App\Models\PastQuestionMaterial;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Models\QuizAttempt;
use App\Models\Subject;


Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');



// Route::get('/scoreboard', ScoreboardDashboard::class)->name('scoreboard');
// // Route::get('quizzes/{quiz}/attempt', [QuizController::class, 'attempt'])->name('quizzes.attempt')->middleware('auth');
// Route::resource('quizzes', QuizController::class)->middleware('auth');;
Route::middleware(['auth'])->group(function (): void {
    Route::post('quiz-attempts/{quizAttempt}/complete', [QuizAttemptController::class, 'complete'])->name('quiz-attempts.complete');
    
    Route::get('/quiz-attempts/{quizAttempt}/review', [QuizAttemptController::class, 'review'])->name('quiz-attempts.review');
    Route::resource('quiz-attempts', QuizAttemptController::class);
    
    Volt::route('quiz-dashboard', 'pages.quiz-dashboard')->name('quiz-dashboard');
    Volt::route('past-questions', 'pages.past-questions')->name('past-questions');
    Volt::route('tutorials', 'pages.tutorials')->name('tutorials');
    
});




Route::view('/donate', 'donate')->name('donate');

Route::middleware(['auth', CheckRole::class . ':admin'])->group(function (): void {
    Volt::route('/admin/scoreboard', 'pages.admin.scoreboard-dashboard')->name('admin.scoreboard');
    Volt::route('/admin/dashboard', 'pages.admin.dashboard')
        ->name('admin.dashboard');
    Volt::route('/admin/materials', 'pages.admin.materials')
        ->name('admin.materials');
    Volt::route('/admin/tutorials', 'pages.admin.tutorials')
        ->name('admin.tutorials');

    // Route Model Binding for singular resources
    Volt::route('/quiz-attempt/{quizAttempt}', 'pages.admin.quiz-attempt')
        ->name('admin.quiz-attempt');

    Volt::route('/admin/subject/{subject}/edit-quiz', 'pages.admin.edit-quiz')
        ->name('admin.edit-quiz');


    //will do to show all the stuffs in a subject
    Volt::route('/admin/subject/{subject}', 'pages.admin.subject')
        ->name('admin.subject');

    // Non-model-bound routes
    Volt::route('/admin/quiz-attempts', 'pages.admin.quiz-attempts')->name('admin.quiz-attempts');
    Volt::route('/admin/subjects', 'pages.admin.subjects')->name('admin.subjects');
});






require __DIR__ . '/auth.php';
