<?php

use App\Models\Subject;
use App\Models\PastQuestionMaterial;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;


new class extends Component {
    use WithFileUploads, Toast;
    
    public PastQuestionMaterial $model;
    
    #[Validate('required|exists:subjects,id')]
    public $subject_id;

    #[Validate('required|integer|min:1900|max:2100')]
    public $year;

    #[Validate('nullable|file|mimes:pdf,doc,docx|max:10240')]
    public $file;

    public $subjects;
    public $existingFile;

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

    public function mount(PastQuestionMaterial $model)
    {
        $this->model = $model;
        $this->subjects = Subject::all();
        $this->subject_id = $model->subject_id;
        $this->year = $model->year;
        $this->existingFile = $model->link;
    }

    protected function saveFile($file, $year, $subject): string
    {
        $fileName = sprintf(
            'past_questions/%s/%s_%s.%s',
            $subject,
            $year,
            uniqid(),
            $file->getClientOriginalExtension()
        );

        return Storage::disk('public')->putFileAs('', $file, $fileName);
    }

    public function update(): void
    {
        if(request()->user()->can('update', $this->model)){
            $data = $this->validate();
            
            $updateData = [
                'subject_id' => $data['subject_id'],
                'year' => $data['year'],
            ];

            if ($this->file) {
                // Delete old file
                Storage::disk('public')->delete($this->existingFile);
                
                // Save new file
                $subject = Subject::find($data['subject_id'])->name;
                $updateData['link'] = $this->saveFile($this->file, $data['year'], $subject);
            }

            $this->model->update($updateData);

            $this->dispatch('model-updated', model: $this->model);
            $this->success(
                'Successful', 
                'Past Question Material Updated Successfully', 
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
        $this->dispatch('model-update-cancelled');
    }
}; ?>

<div>
    <form wire:submit="update" class="p-3">
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
                type="number"
                min="1900"
                max="2100"
                inline
            />
            <x-input-error :messages="$errors->get('year')" class="mt-2" />
        </div>

        <!-- Existing File -->
        <div class="mt-4">
            <p class="text-sm text-gray-600">Current file: 
                <a href="{{ Storage::url($existingFile) }}" 
                   target="_blank" 
                   class="text-blue-500 hover:underline">
                    View Current File
                </a>
            </p>
        </div>

        <!-- File Upload -->
        <div class="mt-4">
            <div class="space-y-2">
                <x-label for="file" value="New File (Leave blank to keep current)" />
                
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
                        New file: {{ $file->getClientOriginalName() }}
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
                label="Update"
                type="submit"
                class="btn-primary"
                wire:loading.attr="disabled"
                wire:target="update"
            />
        </div>
    </form>
</div>