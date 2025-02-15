<?php

use Mary\Traits\Toast;
use App\Models\Category;
use App\Models\Interest;
use Livewire\Attributes\On;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Services\CategoryService;
use App\Services\InterestService;
use Illuminate\Support\Facades\Validator;
use App\Filament\Resources\CategoryResource;
use App\Filament\Resources\InterestResource;
use App\Models\ActionRequest;
use App\Models\Bank;
use App\Models\Subject;
use App\Models\Tutorial;
use Filament\Actions\Action;

new class extends Component
{
    use WithFileUploads, Toast;

    public Tutorial $model;


    #[On('model-changed')]
    public function updateModel(Tutorial $model)
    {
        $this->model = $model;
    }

    #[On('model-updated')]
    public function refreshModel()
    {
        $this->model->refresh();
        // $this->dispatch('model-updated', model: $this->model);
    }
}; ?>

<div>
    <div class="p-6">

        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Subject: {{ $this->model->subject->name  }}

        </h2>

        <div class="border rounded-lg shadow p-4">
            <div class="mt-4">
                <livewire:edit-model-attribute class="border rounded-md" attribute="link" :model="$this->model" rules='required|url' />
            </div>
            <div class="mt-4">
                <livewire:edit-model-attribute class="border rounded-md" attribute="title" :model="$this->model" rules='required|string' />
            </div>
        </div>


        
        
        
        <p class="mt-8 text-xs text-gray-600 dark:text-gray-400">
            Created at: {{ $this->model->created_at  }}. ||
            Updated at: {{ $this->model->updated_at }}
        </p>


        <!-- utility buttons -->
        <div class="flex flex-wrap gap-1 my-3">

        </div>
        <!-- utility buttons -->

        <div class="flex justify-end mt-6">
            <x-secondary-button x-on:click="$dispatch('close-edit')">
                {{ __('Cancel') }}
            </x-secondary-button>
        </div>
    </div>
</div>