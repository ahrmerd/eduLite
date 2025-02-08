<?php

namespace App\Livewire;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Subject;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class ScoreboardDashboard extends Component
{
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
        $query = QuizAttempt::with(['quiz' => function (Builder $query) {
            $query->withCount('questions');
        }])
            // ->withCount(['questions'])
            ->where('status', 'completed');

        if ($this->selectedQuiz) {
            $query->where('quiz_id', $this->selectedQuiz);
        }

        if ($this->dateFrom) {
            $query->where('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->where('created_at', '<=', $this->dateTo);
        }
        $attempts = $query->get();
        // dd($query->get());

        return [
            'total_attempts' => $attempts->count(),
            'average_score' => $attempts->avg(function ($attempt) {
                return ($attempt->score / $attempt->quiz->questions_count) * 100;
            }),
            'top_score' => $attempts->max(function ($attempt) {
                return ($attempt->score / $attempt->quiz->questions_count) * 100;
            }),
            'completion_rate' => round(($query->where('status', 'completed')->count() / max(1, $query->count())) * 100, 2),
        ];
    }

    public function getScoreboardProperty()
    {
        $query = QuizAttempt::with(['user', 'quiz' => function (Builder $query) {
            $query->withCount('questions');
        }])
            ->where('status', 'completed')
            ->when($this->selectedQuiz, function ($q) {
                $q->where('quiz_id', $this->selectedQuiz);
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
            DB::raw('RANK() OVER (PARTITION BY quiz_id ORDER BY (score * 100.0 / (SELECT COUNT(*) FROM questions WHERE quiz_id = quiz_attempts.quiz_id)) DESC) as rank')
        );

        return $query->orderBy('score', 'desc')->paginate($this->perPage);
    }

    public function getQuizzesProperty()
    {
        return Subject::all();
    }

    public function render()
    {
        return view('livewire.scoreboard-dashboard', [
            'analytics' => $this->analytics,
            'scoreboard' => $this->scoreboard,
            'quizzes' => $this->quizzes,
        ]);
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
        $headers = ['Rank', 'User', 'Quiz', 'Score (%)', 'Date'];
        $data = $this->scoreboard;

        $output = fopen('php://output', 'w');
        fputcsv($output, $headers);

        foreach ($data as $row) {
            $percentageScore = ($row->score / $row->quiz->questions_count) * 100;
            fputcsv($output, [
                $row->rank,
                $row->user->name,
                $row->quiz->title,
                number_format($percentageScore, 1) . '%',
                $row->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        fclose($output);
    }
}
