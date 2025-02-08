<?php

use App\Models\QuizAttempt;
use App\Models\Subject;
use Mary\Traits\Toast;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

use App\Models\Tutorial;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

new #[Layout('components.layouts.admin')] class extends Component
{
    use Toast;

    use WithPagination;

    public $selectedQuiz = null;
    public $dateFrom = null;
    public $dateTo = null;
    public $searchTerm = '';
    public $perPage = 10;

    protected $queryString = [
        'selectedQuiz' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'searchTerm' => ['except' => ''],
    ];

    public function getAnalyticsProperty()
    {
        $query = QuizAttempt::with(['subject' => function ($query) {
            $query->withCount('questions');
        }])
            // ->withCount(['questions'])
            ->where('status', 'completed');

        if ($this->selectedQuiz) {
            $query->where('subject_id', $this->selectedQuiz);
        }

        if ($this->dateFrom) {
            $query->where('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->where('created_at', '<=', $this->dateTo);
        }
        $attempts = $query->get();
        // dd($query->get());
// dd($attempts);
        return [
            'total_attempts' => $attempts->count(),
            'average_score' => $attempts->avg(function ($attempt) {
                // dd($attempt->subject->loadCount('questions'));
                return ($attempt->score / $attempt->subject->questions()->count()) * 100;
            }),
            'top_score' => $attempts->max(function ($attempt) {

                return ($attempt->score / $attempt->subject->questions()->count()) * 100;
            }),
            'completion_rate' => round(($query->where('status', 'completed')->count() / max(1, $query->count())) * 100, 2),
        ];
    }

    public function getScoreboardProperty()
    {
        $query = QuizAttempt::with(['user', 'subject' => function ($query) {
            $query->withCount('questions');
        }])
            ->where('status', 'completed')
            ->when($this->selectedQuiz, function ($q) {
                $q->where('subject_id', $this->selectedQuiz);
            })
            ->when($this->dateFrom, function ($q) {
                $q->where('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($q) {
                $q->where('created_at', '<=', $this->dateTo);
            })
            ->when($this->searchTerm, function ($q) {
                $q->whereHas('user', function ($query) {
                    $query->where('name', 'like', '%' . $this->searchTerm . '%');
                });
            });

        // Calculate percentage-based rank
        $query->select(
            'quiz_attempts.*',
            DB::raw('RANK() OVER (PARTITION BY subject_id ORDER BY (score * 100.0 / (SELECT COUNT(*) FROM questions WHERE subject_id = quiz_attempts.subject_id)) DESC) as qrank')
        );

        return $query->orderBy('score', 'desc')->paginate($this->perPage);
    }

    public function getQuizzesProperty()
    {
        return Subject::all();
    }

    public function with()
    {
        return[
            'analytics' => $this->getAnalyticsProperty(),
            'scoreboard' => $this->getScoreboardProperty(),
            'quizzes' => $this->getQuizzesProperty(),
        ];
    }

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function export()
    {
        return response()->streamDownload(function () {
            echo $this->generateCsv();
        }, 'scoreboard-export-' . now()->format('Y-m-d') . '.csv');
    }

    private function generateCsv()
    {
        $headers = ['Rank', 'User', 'Subject', 'Score (%)', 'Date'];
        $data = $this->scoreboard;

        $output = fopen('php://output', 'w');
        fputcsv($output, $headers);

        foreach ($data as $row) {
            $percentageScore = ($row->score / $row->subject->questions_count) * 100;
            fputcsv($output, [
                $row->qrank,
                $row->user->name,
                $row->subject->name,
                number_format($percentageScore, 1) . '%',
                $row->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        fclose($output);
    }
}; ?>

<div>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Analytics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Attempts</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $analytics['total_attempts'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Average Score</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $analytics['average_score'] }}%</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Top Score</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $analytics['top_score'] }}%</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Completion Rate</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $analytics['completion_rate'] }}%</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6 mb-6">
            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                <div class="sm:col-span-2">
                    <label for="quiz" class="block text-sm font-medium text-gray-700">Subjects</label>
                    <select wire:model.live="selectedQuiz" id="quiz" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">All Subjects</option>
                        @foreach($quizzes as $quiz)
                            <option value="{{ $quiz->id }}">{{ $quiz->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label for="dateFrom" class="block text-sm font-medium text-gray-700">Date From</label>
                    <input type="date" wire:model.live="dateFrom" id="dateFrom" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <div class="sm:col-span-2">
                    <label for="dateTo" class="block text-sm font-medium text-gray-700">Date To</label>
                    <input type="date" wire:model.live="dateTo" id="dateTo" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
            </div>
        </div>

        <!-- Scoreboard Table -->
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex justify-between mb-4">
                    <div class="w-1/3">
                        <input type="text" wire:model.live.debounce.300ms="searchTerm" placeholder="Search users..." class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <button wire:click="export" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        Export CSV
                    </button>
                </div>

                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quiz</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($scoreboard as $attempt)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $attempt->qrank }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $attempt->user->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $attempt->subject->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ($attempt->score/$attempt->subject->questions_count)*100 }}%</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $attempt->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $scoreboard->links() }}
                </div>
            </div>
        </div>
    </div>
</div>