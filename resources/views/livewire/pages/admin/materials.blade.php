<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Subject;
use App\Models\PastQuestionMaterial;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new #[Layout('components.layouts.admin')] class extends Component
{
    use WithPagination;
    use Toast;

    public int $perPage = 10;
    public $confirmAction = null;
    public ?PastQuestionMaterial $model = null;
    public array $sortBy = ['column' => 'id', 'direction' => 'desc'];
    public array $selected = [];

    public $subjects;
    public $year = '';
    public $link = '';
    public $subject_id = '';
    public $search = '';

    public bool $editModal = false;
    public bool $filterDrawer = false;
    public bool $viewModal = false;
    public bool $createModal = false;
    public bool $confirmModal = false;
    public bool $importModal = false;
    public $deleteId = 0;

    public function mountModel($modelId): PastQuestionMaterial
    {
        return PastQuestionMaterial::findOrFail($modelId);
    }

    #[Computed()]
    public function hasModel()
    {
        return $this->model != null;
    }

    public function setModel(PastQuestionMaterial $model)
    {
        $this->model = $model;
        $this->dispatch('model-changed', model: $model->id);
    }

    public function updated($property): void
    {
        if (!is_array($property) && $property != "") {
            $this->resetPage();
        }
    }

    public function clear(): void
    {
        $this->reset();
        $this->resetPage();
        $this->success('Filters cleared.', position: 'toast-bottom');
    }

    public function delete($id)
    {
        $this->deleteId = $id;
        $this->confirm('delete');
    }

    public function confirmedDeleteSelected()
    {
        $models = PastQuestionMaterial::query()->findMany($this->selected);

        $models->each(function ($model): void {
            $model->delete();
        });
    }

    public function confirmedDelete()
    {
        $material = PastQuestionMaterial::query()->findOrFail($this->deleteId);
        $material->delete();
        $this->deleteId = 0;
    }

    public function confirm($action)
    {
        $this->confirmAction = $action;
        $this->confirmModal = true;
    }

    public function confirmed()
    {
        $return = null;
        switch ($this->confirmAction) {
            case 'delete':
                $this->confirmedDelete();
                break;
            case 'bulk delete':
                $this->confirmedDeleteSelected();
                break;
        }

        $this->confirmModal = false;
        $this->confirmAction  = false;
        return $return;
    }

    public function edit(PastQuestionMaterial $model)
    {
        $this->setModel($model);
        $this->editModal = true;
    }

    public function openCreateModal()
    {
        $this->createModal = true;
    }

    #[On('close-create')]
    public function closeCreateModal()
    {
        $this->createModal = false;
    }
    #[On('model-updated'), On('model-created')]
    public function refreshModels()
    {
        $this->models = $this->models();
    }


    public function deleteSelected()
    {
        $this->confirm('bulk delete');
    }

    public function with()
    {
        return [
            'headers' => $this->headers(),
            'models' => $this->models()
        ];
    }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#'],
            ['key' => 'subject.name', 'label' => 'Subject'],
            ['key' => 'year', 'label' => 'Year'],
            ['key' => 'link', 'label' => 'Link'],
        ];
    }

    public function models()
    {
        $query = PastQuestionMaterial::query()
            ->with('subject');

        if ($this->search) {
            $query->whereHas('subject', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })
            ->orWhere('year', 'like', '%' . $this->search . '%')
            ->orWhere('link', 'like', '%' . $this->search . '%');
        }
        
        $query->orderBy(...array_values($this->sortBy));

        return $query->paginate($this->perPage);
    }
}
?>


<div class="overflow-auto dark:bg-gray-900">
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Past Question Materials</h1>
            </div>
            <div class="mt-4 md:mt-0">
                <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full">
                    Total Records: {{ PastQuestionMaterial::count() }}
                </span>
            </div>
        </div>
        <div class="mt-4 border-b border-gray-200 dark:border-gray-700"></div>
    </div>

    <!-- Search and Filters Header -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex-1 max-w-2xl">
                <x-mary-input
                    class="w-full dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700"
                    clearable
                    placeholder="Search..."
                    icon="o-magnifying-glass"
                    wire:model.live="search" />
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Search by subject, year, or link</p>
            </div>
            <x-mary-button
                label="Advanced Filters"
                icon="o-adjustments-horizontal"
                class="btn-outline dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700" />
        </div>
    </div>

    <!-- Actions Bar -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="text-sm font-medium dark:text-gray-200">Show:</span>
                <select wire:model.live="perPage" class="select select-bordered select-sm text-xs dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="text-sm text-gray-500 dark:text-gray-400">entries</span>
            </div>

            <div class="flex flex-wrap justify-end gap-2">
                <div class="space-x-2">
                    <x-mary-button
                        wire:click="openCreateModal"
                        label="Add Material"
                        icon="o-plus"
                        class="btn-primary dark:bg-blue-600 dark:hover:bg-blue-700" />
                </div>
                <div class="space-x-2">
                    <x-mary-button
                        wire:click="deleteSelected"
                        label="Delete Selected"
                        icon="o-trash"
                        class="btn-error btn-outline dark:border-red-600 dark:text-red-400 dark:hover:bg-red-900" />
                </div>
            </div>
        </div>
    </div>

    <div class="overflow-hidden bg-white dark:bg-gray-800 rounded-lg shadow">
        <x-table 
            selectable-key="id" 
            selectable 
            striped 
            wire:model="selected" 
            :headers="$headers" 
            :rows="$models"
            :sort-by="$sortBy" 
            @row-selection="console.log($event.detail)" 
            with-pagination
            class="dark:text-gray-200">
            @scope('prependActions', $model)
            <div class="flex gap-1">
                <x-mary-button 
                    icon="o-pencil-square" 
                    wire:click="edit({{ $model->id }})" 
                    spinner
                    class="p-1 border-none btn-sm btn-warning btn-outline dark:text-yellow-400 dark:hover:bg-yellow-900" />
                <x-mary-button 
                    icon="o-trash" 
                    wire:click="delete({{ $model->id }})" 
                    spinner
                    class="p-1 border-none btn-sm btn-error btn-outline dark:text-red-400 dark:hover:bg-red-900" />
            </div>
            @endscope
            @scope('cell_link', $material)
                   <a href='{{ Storage::url($material->link)}}' download class="text-blue-600 dark:text-blue-400 hover:underline"> Download </a>
            @endscope
        </x-table>
    </div>

    <x-mary-modal wire:model="createModal" class="backdrop-blur dark:bg-gray-900/90" title="Add New Past Question Material">
        <livewire:admin.create-past-question-material />
    </x-mary-modal>

    <x-mary-modal wire:model="editModal" class="backdrop-blur dark:bg-gray-900/90" title="Edit Past Question Material">
        @if ($this->model != null)
        <livewire:admin.edit-past-question-material :model="$this->model" />
        @endif
    </x-mary-modal>

    <x-mary-modal wire:model="confirmModal" title="Confirm Action" class="backdrop-blur dark:bg-gray-900/90">
        <div class="relative">
            <div wire:loading.delay wire:target="confirmed">
                <div class="absolute inset-0 bg-white/50 dark:bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-50">
                    <div class="text-center p-8 rounded-lg">
                        <div class="relative w-20 h-20 mx-auto mb-4">
                            <div class="absolute inset-0 border-4 border-blue-200 dark:border-blue-800 border-t-blue-500 rounded-full animate-spin"></div>
                            <div class="absolute inset-3 bg-blue-500 dark:bg-blue-600 rounded-full animate-[pulse_1s_infinite]"></div>
                        </div>
                        <p class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                            Loading....
                        </p>
                        <div class="text-blue-500 dark:text-blue-400 text-2xl">
                            <span class="loading-dot">.</span>
                            <span class="loading-dot">.</span>
                            <span class="loading-dot">.</span>
                        </div>
                    </div>
                </div>
            </div>

            <x-mary-alert icon="o-exclamation-triangle" class="alert-warning dark:bg-yellow-900/50 dark:text-yellow-200">
                <p class="text-lg">Are you sure you want to {{ $confirmAction }}?</p>
                <p class="text-sm mt-2 dark:text-gray-300">This action cannot be undone.</p>
            </x-mary-alert>
            <x-slot:actions>
                <div class="flex justify-end gap-2">
                    <x-mary-button 
                        label="Cancel" 
                        @click="$wire.confirmModal = false" 
                        class="btn-outline dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700" />
                    <x-mary-button
                        label="Confirm"
                        wire:click="confirmed"
                        wire:loading.attr="disabled"
                        wire:target="confirmed"
                        class="btn-error dark:bg-red-600 dark:hover:bg-red-700" />
                </div>
            </x-slot:actions>
        </div>
    </x-mary-modal>
</div>