<x-app-layout>
    <x-slot name="header">
        <h2 class="mb-6 text-2xl font-bold">Title: {{ $subject->name }}</h2>
        <p class="mb-4 text-gray-700"><strong>Time Limit:</strong> {{ $subject->loadCount('questions')->calculateTimeLimit() }} minutes</p>

    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                <livewire:quiz-attempt-show :quizAttempt="$quizAttempt" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>