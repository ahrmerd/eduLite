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
use Filament\Actions\Action;

new class extends Component
{
    use WithFileUploads, Toast;

    public Subject $model;


    #[On('model-changed')]
    public function updateModel(Subject $model)
    {
        $this->model = $model;
        // dump($this->model);
        // dump($model);
        // $this->reasonForRequest = '';
        // $this->dispatch('reset-form', attrs: $model);
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
            Name: {{ $this->model->name  }}

        </h2>




        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Created at: {{ $this->model->created_at  }}. ||
            Updated at: {{ $this->model->updated_at }}
        </p>

        <livewire:edit-model-attribute class="border rounded-md" attribute="name" :model="$this->model" rules='required|string' />





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