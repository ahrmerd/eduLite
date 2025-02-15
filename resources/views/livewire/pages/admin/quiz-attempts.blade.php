<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\Subject;
use App\Models\Question;
use App\Models\QuizAttempt;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

new #[Layout('components.layouts.admin')] class extends Component
// new  class extends Component
{
    use WithPagination;


    public function getQuizAttempts(){
        return  QuizAttempt::with(['subject' => function ($query) {
            $query->withCount('questions');
        }])->paginate();
    }

    public function with(){
        return [
            'quizAttempts'=> $this->getQuizAttempts()
        ];
    }

}
?>

<div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Quiz Attempts</h1>
            </div>
            
        </div>
        <div class="mt-4 border-b border-gray-200 dark:border-gray-700"></div>
    </div>
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if($quizAttempts->isEmpty())
                    <p class="text-gray-600">There arent any attempted quizzes yet.</p>
                    @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="">
                                <tr>
                                    <th class="px-4 py-2 text-left">User</th>
                                    <th class="px-4 py-2 text-left">Subject</th>
                                    <th class="px-4 py-2 text-left">Status</th>
                                    <th class="px-4 py-2 text-left">Score</th>
                                    <th class="px-4 py-2 text-left">Started At</th>
                                    <th class="px-4 py-2 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($quizAttempts as $attempt)
                                <tr class="border-b">
                                    <td class="px-4 py-2">{{ $attempt->user->name }}</td>
                                    <td class="px-4 py-2">{{ $attempt->subject->name }}</td>
                                    <td class="px-4 py-2">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $attempt->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                       ($attempt->status === 'progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ ucfirst($attempt->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">
                                        {{ $attempt->status === 'completed' ? ($attempt->score/$attempt->subject->questions_count)*100 . '%' : 'N/A' }}
                                    </td>
                                    <td class="px-4 py-2">{{ $attempt->created_at->format('M d, Y H:i') }}</td>
                                    <td class="px-4 py-2">
                                        @if($attempt->status === 'progress')
                                        <p>...</p>
                                        @elseif($attempt->status === 'completed')
                                        <a href="{{ route('admin.quiz-attempt', $attempt) }}"
                                            class="text-green-600 hover:text-green-800">Review</a>
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