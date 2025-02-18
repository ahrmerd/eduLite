<?php

use App\Models\PastQuestionMaterial;
use App\Models\Subject;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $subjectFilter = '';
    public $yearFilter = '';

    public function getPastQuetions()
    {
        return PastQuestionMaterial::query()
            ->when($this->search, function ($query) {
                return $query->whereHas('subject', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->subjectFilter, function ($query) {
                return $query->where('subject_id', $this->subjectFilter);
            })
            ->when($this->yearFilter, function ($query) {
                return $query->where('year', 'like', '%' . $this->yearFilter . '%');
            })
            ->paginate(10);
    }

    public function with()
    {
        return [
            'materials' => $this->getPastQuetions(),
            'subjects' => Subject::all(),
        ];
    }
}; ?>

<div class="container mx-auto px-4 py-8 dark:bg-gray-900">
    <h1 class="text-3xl font-bold text-center text-maroon-800 dark:text-maroon-200 mb-4">Past Questions</h1>
    <p class="text-center text-gray-600 dark:text-gray-400 mb-8">Download or view past questions by subject and year.</p>

    <!-- Filters -->
    <div class="flex flex-col md:flex-row gap-4 mb-8">
        <input
            type="text"
            wire:model.live="search"
            placeholder="Search by subject..."
            class="flex-1 p-2 border border-gray-300 dark:border-gray-600 rounded-lg 
                   bg-white dark:bg-gray-800 
                   text-gray-900 dark:text-gray-100
                   focus:outline-none focus:ring-2 focus:ring-maroon-500 
                   placeholder-gray-500 dark:placeholder-gray-400" />
        <select
            wire:model.live="subjectFilter"
            class="flex-1 p-2 border border-gray-300 dark:border-gray-600 rounded-lg 
                   bg-white dark:bg-gray-800 
                   text-gray-900 dark:text-gray-100
                   focus:outline-none focus:ring-2 focus:ring-maroon-500">
            <option value="">All Subjects</option>
            @foreach($subjects as $subject)
            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
            @endforeach
        </select>
        <input
            wire:model.live="yearFilter"
            placeholder="Filter by year..."
            class="flex-1 p-2 border border-gray-300 dark:border-gray-600 rounded-lg 
                   bg-white dark:bg-gray-800 
                   text-gray-900 dark:text-gray-100
                   focus:outline-none focus:ring-2 focus:ring-maroon-500
                   placeholder-gray-500 dark:placeholder-gray-400" />
    </div>

    <!-- Materials Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($materials as $material)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md dark:shadow-gray-900 
                    p-6 hover:shadow-lg transition-shadow">
            <h2 class="text-xl font-semibold text-maroon-800 dark:text-maroon-200 mb-4">
                {{ $material->subject->name }}
            </h2>
            <ul class="space-y-2">
                <li>
                    <a href="{{ Storage::url($material->link) }}"
                       download
                       class="text-maroon-600 dark:text-maroon-300 
                              hover:text-maroon-800 dark:hover:text-maroon-100 
                              hover:underline">
                        {{ $material->year }}
                    </a>
                </li>
            </ul>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-8 dark:text-gray-200">
        {{ $materials->links() }}
    </div>
</div>