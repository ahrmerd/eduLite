<?php

use App\Http\Middleware\CheckRole;
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





Route::middleware(['auth', CheckRole::class . ':admin'])->group(function (): void {
    Volt::route('/admin', 'pages.admin.dashboard')
        ->name('admin.dashboard');
    Volt::route('/admin/materials', 'pages.admin.materials')
        ->name('admin.materials');
    Volt::route('/admin/tutorials', 'pages.admin.tutorials')
        ->name('admin.tutorials');

    // Route Model Binding for singular resources
    Volt::route('/quiz-attempt/{quizAttempt}', 'pages.admin.quiz-attempt')
        ->name('admin.quiz-attempt');


    //will do to show all the stuffs in a subject
    Volt::route('/admin/subject/{subject}', 'pages.admin.subject')
        ->name('admin.subject');

    // Non-model-bound routes
    Volt::route('/admin/quiz-attempts', 'pages.admin.quiz-attempts')->name('admin.quiz-attempts');
    Volt::route('/admin/subjects', 'pages.admin.subjects')->name('admin.subjects');
});






require __DIR__ . '/auth.php';
