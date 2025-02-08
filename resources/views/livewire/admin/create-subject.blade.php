<?php

use App\Models\Bank;
use App\Models\Subject;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Mary\Traits\Toast;


new class extends Component
{
    use WithFileUploads, Toast;

    #[Validate('required|string|unique:subjects,name')]
    public $name;


    public function create(): void
    {
        
        $data = $this->validate();

        Subject::query()->create($data);
        $this->dispatch('close-create');

        $this->reset();
        $this->success('Successfull', 'Created Successfully', 'toast-top toast-center' );
    }


    public function cancelled(): void
    {
        $this->dispatch('close-create');
    }
}; ?>

<div>
    <form wire:submit="create" class="p-3">

        <!-- Bank -->
        <div class="mt-2">
            <x-mary-input wire:model="name" label="Name" inline name='name' />
        </div>
       
        <div class="mt-3">
            <x-primary-button class="ml-4">
                Create
            </x-primary-button>

            <x-secondary-button class="ml-4" wire:click="cancelled">
                Close
            </x-secondary-button>
        </div>
        
    </form>
</div>