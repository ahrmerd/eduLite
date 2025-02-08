<?php

use App\Models\Subject;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {

    use WithPagination;

    public $activeTab = 'available';


    public $perPage = 10;

    #[Computed]
    public function availableQuizzesCount()
    {
        return $this->baseQuery()->whereNotIn('id', auth()->user()->quizAttempts->pluck('subject_id'))->count();
    }
    #[Computed]
    public function inProgressAttemptsCount()
    {
        return auth()->user()->quizAttempts()
            ->with(['subject' => function ($query) {
                $query->withCount('questions');
            }])
            ->where('status', 'progress')
            ->count();
    }
    #[Computed]
    public function completedAttemptsCount()
    {
        return auth()->user()->quizAttempts()
            ->with(['subject' => function ($query) {
                $query->withCount('questions');
            }])
            ->where('status', 'completed')
            ->count();
    }

    public function baseQuery()
    {
        return Subject::withCount('questions');
    }

    #[Computed]
    public function availableQuizzes()
    {
        return $this->baseQuery()->whereNotIn('id', auth()->user()->quizAttempts->pluck('subject_id'))
            ->latest()
            ->paginate($this->perPage);
    }

    #[Computed]
    public function inProgressAttempts()
    {
        return auth()->user()->quizAttempts()
            ->with(['subject' => function ($query) {
                $query->withCount('questions');
            }])
            ->where('status', 'progress')
            // ->with('quiz')
            ->paginate($this->perPage);
    }

    #[Computed]
    public function completedAttempts()
    {
        return auth()->user()->quizAttempts()
            ->with(['subject' => function ($query) {
                $query->withCount('questions');
            }])
            ->where('status', 'completed')
            // ->with('quiz')
            ->latest()
            ->paginate($this->perPage);
    }
}; ?>

<div>
    <div class="min-h-screen bg-gray-100 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl rounded-lg">
                <!-- User Stats -->
                <div class="p-6 border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-blue-700">Available Quizzes (New)</h3>
                            <p class="text-3xl font-bold text-blue-900">{{ $this->availableQuizzesCount }}</p>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-yellow-700">In Progress</h3>
                            <p class="text-3xl font-bold text-yellow-900">{{ $this->inProgressAttemptsCount }}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-green-700">Completed</h3>
                            <p class="text-3xl font-bold text-green-900">{{ $this->completedAttemptsCount }}</p>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <div x-data="{ activeTab: $wire.activeTab }" x-on:tab-changed="activeTab = $event.detail">
                    <div class="border-b border-gray-200">
                        <nav class="flex -mb-px">
                            <button @click="activeTab = 'available'"
                                :class="{'border-blue-500 text-blue-600': activeTab === 'available'}"
                                class="w-1/3 py-4 px-1 text-center border-b-2 font-medium text-sm">
                                Available Quizzes
                            </button>
                            <button @click="activeTab = 'progress'"
                                :class="{'border-blue-500 text-blue-600': activeTab === 'progress'}"
                                class="w-1/3 py-4 px-1 text-center border-b-2 font-medium text-sm">
                                In Progress
                            </button>
                            <button @click="activeTab = 'completed'"
                                :class="{'border-blue-500 text-blue-600': activeTab === 'completed'}"
                                class="w-1/3 py-4 px-1 text-center border-b-2 font-medium text-sm">
                                Completed
                            </button>
                        </nav>
                    </div>

                    <!-- Tab Contents -->
                    <div class="p-6">
                        <div x-show="activeTab === 'available'">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($this->availableQuizzes as $quiz)
                                <div class="border rounded-lg p-4 hover:shadow-lg transition">
                                    <h3 class="text-lg font-semibold mb-2">{{ $quiz->name }}</h3>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-500">
                                            {{ $quiz->loadCount('questions')->calculateTimeLimit(). ' minutes'}}
                                            {{ $quiz->questions_count. ' Questions'}}
                                        </span>
                                        <form action="{{ route('quiz-attempts.store', $quiz->id) }}" method="post">
                                            @csrf
                                            <input type="text" hidden name="subject_id" value="{{$quiz->id}}" id="">
                                            <button type="submit" class="text-green-500 hover:underline">Take Quiz</button>
                                        </form>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            {{$this->availableQuizzes->links()}}
                        </div>

                        <div x-show="activeTab === 'progress'">
                            <div class="space-y-4">
                                @foreach($this->inProgressAttempts as $attempt)
                                <div class="border rounded-lg p-4">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <h3 class="text-lg font-semibold">{{ $attempt->subject->name }}</h3>
                                            <p class="text-sm text-gray-500">Started: {{ $attempt->started_at->diffForHumans() }}</p>
                                        </div>
                                        <a href="{{ route('quiz-attempts.show', $attempt) }}"
                                            class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600">
                                            Continue
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            {{$this->inProgressAttempts->links()}}

                        </div>

                        <div x-show="activeTab === 'completed'">
                            <div class="space-y-4">
                                @foreach($this->completedAttempts as $attempt)
                                <div class="border rounded-lg p-4">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <h3 class="text-lg font-semibold">{{ $attempt->subject->name }}</h3>
                                            <p class="text-sm text-gray-500">Completed: {{ $attempt->updated_at->diffForHumans() }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-lg font-bold text-green-600">Score: {{ $attempt->score / $attempt->subject->questions_count * 100 }}%</p>
                                            <a href="{{ route('quiz-attempts.review', $attempt) }}"
                                                class="text-blue-500 hover:text-blue-600 text-sm">
                                                Review Quiz
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            {{$this->completedAttempts->links()}}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>