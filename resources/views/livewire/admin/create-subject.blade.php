<?php
use App\Models\Subject;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Mary\Traits\Toast;

new class extends Component
{
    use WithFileUploads, Toast;

    // Define properties with validation rules
    #[Validate('required|string|unique:subjects,name')]
    public $name;

    #[Validate('nullable|string')]
    public $description;

    #[Validate('integer|min:1')]
    public $questions_per_quiz = 30; // Default value

    #[Validate('numeric|min:0.1')]
    public $mins_per_question = 2; // Default value

    /**
     * Create a new subject.
     */
    public function create(): void
    {
        // Authorize the user to create a subject
        $this->authorize('create', Subject::class);

        // Validate input data
        $data = $this->validate();

        // Create the subject in the database
        Subject::query()->create($data);

        // Dispatch an event to close the modal or form
        $this->dispatch('close-create');
        $this->dispatch('model-created');

        // Reset form fields
        $this->reset();

        // Show success toast notification
        $this->success('Success!', 'Subject created successfully.', 'toast-top toast-center');
    }

    /**
     * Handle cancellation of the form.
     */
    public function cancelled(): void
    {
        // Dispatch an event to close the modal or form
        $this->dispatch('close-create');

        // Optionally reset form fields
        $this->reset();
    }
};
?>
<div>
    <form wire:submit="create" class="p-3">
        <!-- Name Field -->
        <div class="mt-2 p-3  border-b">
            <x-mary-input wire:model="name" label="Name" placeholder="Enter subject name" />
           
        </div>

        <!-- Description Field -->
        <div class="mt-2 p-3 border-b">
            <x-mary-textarea hint='This Field is not required' wire:model="description" label="Description" placeholder="Enter subject description" inline />  
        </div>

        <!-- Questions Per Quiz Field -->
        <div class="mt-2 p-3  border-b">
            <x-mary-input type="number" wire:model="questions_per_quiz" label="Questions Per Quiz" placeholder="Enter number of questions" />
          
        </div>

        <!-- Minutes Per Question Field -->
        <div class="mt-2 p-3  border-b">
            <x-mary-input type="number" step="0.1" wire:model="mins_per_question" label="Minutes Per Question" placeholder="Enter minutes per question" />
            
        </div>

        <!-- Action Buttons -->
        <div class="mt-3 flex gap-2">
            <x-primary-button type="submit">
                Create
            </x-primary-button>
            <x-secondary-button wire:click="cancelled">
                Close
            </x-secondary-button>
        </div>
    </form>
</div>