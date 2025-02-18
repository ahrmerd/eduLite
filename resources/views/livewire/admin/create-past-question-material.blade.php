<?php

use App\Models\Subject;
use App\Models\PastQuestionMaterial;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads, Toast;
    
    #[Validate('required|exists:subjects,id')]
    public $subject_id = '';

    #[Validate('required')]
    public $year = '';

    #[Validate('required|file|mimes:pdf,doc,docx|max:10240')] // 10MB max
    public $file;

    public $subjects;

    public function mount()
    {
        $this->subjects = Subject::all();
    }


    // public function subjects(){

    // }

    protected function saveFile($file, $year, $subject): string
    {
        $fileName = sprintf(
            'past_questions/%s/%s_%s.%s',
            $subject,
            $year,
            uniqid(),
            $file->getClientOriginalExtension()
        );

        // dd($fileName);

        return Storage::disk('public')->putFileAs('', $file, $fileName);
    }

    public function create(): void
    {
        if(request()->user()->can('create', PastQuestionMaterial::class)){
            $data = $this->validate();
            
            // Get subject name for file path
            $subject = Subject::find($data['subject_id'])->name;

            // dump($subject);
            // dd($data);
            
            // Save file and get path
            $filePath = $this->saveFile($this->file, $data['year'], $subject);
            
            $material = PastQuestionMaterial::query()->create([
                'subject_id' => $data['subject_id'],
                'year' => $data['year'],
                'link' => $filePath
            ]);

            $this->dispatch('model-created', material: $material);
            $this->reset(['subject_id', 'year', 'file']);
            $this->success(
                'Successful', 
                'Past Question Material Created Successfully', 
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
        $this->dispatch('model-create-cancelled');
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
        
        <!-- Year -->
        <div class="mt-4">
            <x-mary-input
                wire:model="year"
                label="Year"
                inline
            />
            <x-input-error :messages="$errors->get('year')" class="mt-2" />
        </div>

        <!-- File Upload -->
        <div class="mt-4">
            <div class="space-y-2">
                <x-label for="file" value="Past Question File" />
                
                <div class="flex items-center justify-center w-full">
                    <label for="file-upload" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                            </svg>
                            <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                            <p class="text-xs text-gray-500">PDF, DOC, or DOCX (MAX. 10MB)</p>
                        </div>
                        <input 
                            wire:model="file"
                            id="file-upload"
                            type="file"
                            accept=".pdf,.doc,.docx"
                            class="hidden"
                        />
                    </label>
                </div>

                @if($file)
                    <div class="mt-2 text-sm text-gray-500">
                        Selected file: {{ $file->getClientOriginalName() }}
                    </div>
                @endif

                <div wire:loading wire:target="file" class="mt-2">
                    <div class="text-sm text-blue-500">Uploading...</div>
                </div>

                <x-input-error :messages="$errors->get('file')" class="mt-2" />
            </div>
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
                wire:loading.attr="disabled"
                wire:target="create"
            />
        </div>
    </form>
</div>