<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\Subject;
use App\Models\Question;
use App\Models\QuizAttempt;
use Illuminate\Support\Facades\DB;

new #[Layout('components.layouts.admin')] class extends Component
// new  class extends Component
{
    // State Properties
    public string $activeSection = 'dashboard';
    public array $stats = [];
    public $recentActivity = [];

    // Computed Properties
    public function stats()
    {
        return [
            [
                'label' => 'Total Users',
                'value' => User::count(),
                'change' => '+' . User::where('created_at', '>=', now()->subWeek())->count()
            ],
            [
                'label' => 'Active Subjects',
                'value' => Subject::count(),
                'change' => '+' . Subject::where('created_at', '>=', now()->subWeek())->count()
            ],
            [
                'label' => 'Total Questions',
                'value' => Question::count(),
                'change' => '+' . Question::where('created_at', '>=', now()->subWeek())->count()
            ],
            [
                'label' => 'Quiz Attempts',
                'value' => QuizAttempt::count(),
                'change' => '+' . QuizAttempt::where('created_at', '>=', now()->subWeek())->count()
            ]
        ];
    }

    public function recentActivity()
    {
        return QuizAttempt::with(['user', 'subject'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($attempt) {
                return [
                    'user' => $attempt->user->name,
                    'action' => $attempt->status,
                    'subject' => $attempt->subject->name,
                    'time' => $attempt->created_at->diffForHumans()
                ];
            });
    }

    // Lifecycle Hooks
    public function mount()
    {
        $this->stats = $this->stats();
        $this->recentActivity = $this->recentActivity();
    }

    // Actions
    public function setActiveSection($section)
    {
        $this->activeSection = $section;
    }
}
?>
<!-- Main Content -->
<div class="flex-1 overflow-auto">
    <header class="bg-white shadow">
        <div class="px-6 py-4">
            <h2 class="text-xl font-semibold text-gray-800">Dashboard Overview</h2>
        </div>
    </header>

    <main class="p-6">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            @foreach($stats as $stat)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-500">{{ $stat['label'] }}</h3>
                <div class="mt-2 flex items-baseline">
                    <p class="text-2xl font-semibold text-gray-900">{{ $stat['value'] }}</p>
                    <span class="ml-2 text-sm font-medium text-green-600">{{ $stat['change'] }}</span>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
                <div class="mt-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($recentActivity as $activity)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $activity['user'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $activity['action'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $activity['subject'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $activity['time'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>