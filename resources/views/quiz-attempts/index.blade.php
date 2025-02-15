<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Show Quizzes
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                @if($quizAttempts->isEmpty())
        <p class="text-gray-600">You haven't attempted any quizzes yet.</p>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Quiz Title</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Score</th>
                        <th class="px-4 py-2 text-left">Started At</th>
                        <th class="px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quizAttempts as $attempt)
                        <tr class="border-b">
                            <td class="px-4 py-2">{{ $attempt->quiz->title }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $attempt->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                       ($attempt->status === 'progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($attempt->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                {{ $attempt->status === 'completed' ? ($attempt->score/$attempt->quiz->questions_count)*100 . '%' : 'N/A' }}
                            </td>
                            <td class="px-4 py-2">{{ $attempt->created_at->format('M d, Y H:i') }}</td>
                            <td class="px-4 py-2">
                                @if($attempt->status === 'progress')
                                    <a href={{ route('quiz-attempts.show', $attempt) }} 
                                       class="text-blue-600 hover:text-blue-800">Continue</a>
                                @elseif($attempt->status === 'completed')
                                    <a href="{{ route('quiz-attempts.show', $attempt) }}" 
                                       class="text-green-600 hover:text-green-800">View Results</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $quizAttempts->links() }}
        </div>
    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>