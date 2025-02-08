<?php
use App\Models\Subject;
use App\Models\Tutorial;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Mary\Traits\Toast;

new class extends Component {
    use WithFileUploads, Toast;
    
    #[Validate('required|exists:subjects,id')]
    public $subject_id = '';
    
    #[Validate('required|url')]
    public $link = '';

    public $subjects;

    public function mount()
    {
        $this->subjects = Subject::all();
    }

    public function create(): void
    {
        if(request()->user()->can('create', Tutorial::class)){
            $data = $this->validate();
            
            $tutorial = Tutorial::query()->create([
                'subject_id' => $data['subject_id'],
                'link' => $data['link']
            ]);

            $this->dispatch('tutorial-created', tutorial: $tutorial);
            $this->reset(['subject_id', 'link']);
            $this->success(
                'Successful', 
                'Tutorial Created Successfully', 
                'toast-top toast-center'
            );
        } else {
            $this->error(
                'Failed', 
                'You are not authorized', 
                'toast-top toast-center'
            );
            $this->cancelled();
        }
    }

    public function cancelled(): void
    {
        $this->dispatch('tutorial-create-cancelled');
    }
}; ?>

<div>
    <form wire:submit="create" class="p-3">
        <!-- Subject -->
        <div class="mt-2">
            <x-mary-select
                wire:model="subject_id"
                label="Select Subject"
                placeholder="Choose a subject"
                :options="$subjects"
                option-label="name"
                option-value="id"
                inline
            />
            <x-input-error :messages="$errors->get('subject_id')" class="mt-2" />
        </div>
        
        <!-- Link -->
        <div class="mt-4">
            <x-mary-input
                wire:model="link"
                label="Tutorial Link"
                hint='A Youtube Link'
                type="url"
                inline
            />
            <x-input-error :messages="$errors->get('link')" class="mt-2" />
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <x-mary-button
                label="Close"
                wire:click="cancelled"
                class="btn-outline"
            />
            <x-mary-button
                label="Create"
                type="submit"
                class="btn-primary"
            />
        </div>
    </form>
</div>