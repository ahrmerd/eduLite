<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');


use Livewire\Volt\Volt;
use App\Models\QuizAttempt;
use App\Models\Subject;

Volt::route('/admin', 'pages.admin.dashboard')
    ->name('admin.dashboard');
Volt::route('/materials', 'pages.admin.materials')
    ->name('admin.materials');
Volt::route('/tutorials', 'pages.admin.tutorials')
    ->name('admin.tutorials');

// Route Model Binding for singular resources
Volt::route('/quiz-attempt/{quizAttempt}', 'pages.admin.quiz-attempt')
    ->name('admin.quiz-attempt');

Volt::route('/subject/{subject}', 'pages.admin.subject')
    ->name('admin.subject');

// Non-model-bound routes
Volt::route('/quiz-attempts', 'pages.admin.quiz-attempts')->name('admin.quiz-attempts');
Volt::route('/subjects', 'pages.admin.subjects')->name('admin.subjects');




require __DIR__ . '/auth.php';
