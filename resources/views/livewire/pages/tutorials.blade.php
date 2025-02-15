<?php

use App\Models\PastQuestionMaterial;
use App\Models\Subject;
use App\Models\Tutorial;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {

    use WithPagination;

    public $search = '';
    public $subjectFilter = '';
    // public $yearFilter = '';


    public function getTutorials(){
       return Tutorial::query()
       ->when($this->search, function ($query) {
           return $query->where('title', 'like', '%' . $this->search . '%');
       })
       ->when($this->subjectFilter, function ($query) {
           return $query->where('subject_id', $this->subjectFilter);
       })
       ->paginate(10); 
    }


    public function with(){
        return [
            'tutorials' => $this->getTutorials(),
            'subjects' => Subject::all(),
        ];
    }

}; ?>
<div class="container mx-auto px-4 py-8 bg-white text-gray-900 dark:bg-gray-900 dark:text-white min-h-screen transition-colors duration-300">
    <h1 class="text-3xl font-bold text-center text-red-500 dark:text-red-400 mb-4">Tutorials</h1>
    <p class="text-center text-gray-600 dark:text-gray-400 mb-8">Watch tutorials by subject.</p>

    <!-- Filters -->
    <div class="flex flex-col md:flex-row gap-4 mb-8">
        <input
            type="text"
            wire:model.live="search"
            placeholder="Search by title..."
            class="flex-1 p-2 border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
        />
        <select
            wire:model.live="subjectFilter"
            class="flex-1 p-2 border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
        >
            <option value="">All Subjects</option>
            @foreach($subjects as $subject)
                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Tutorials Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($tutorials as $tutorial)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                <h2 class="text-xl font-semibold text-red-600 dark:text-red-400 mb-4">{{ $tutorial->title }}</h2>
                <div class="aspect-w-16 aspect-h-9">
                    <iframe
                        src="https://www.youtube.com/embed/{{ $tutorial->getYouTubeId() }}"
                        class="w-full h-full rounded-lg"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                    ></iframe>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $tutorials->links('pagination::tailwind') }}
    </div>
</div>

