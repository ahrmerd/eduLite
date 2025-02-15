<?php

use Mary\Traits\Toast;
use Livewire\Volt\Component;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Livewire\Attributes\On;

new class extends Component
{ 
    use Toast;

    public Model $model;
    public string $attribute;
    public $type;
    public $value = '';
    public $rules;
    public $isEditing = false;
    public $label;

    public function mount(Model $model){
        $attr = $this->attribute;
        $this->value = $model->$attr;
        $this->model = $model;
        $this->label = Str::headline(Str::endsWith($this->attribute, '_id') ? Str::replaceLast('_id', '', $this->attribute) : $this->attribute);
    }

    public function toggleEdit()
    {
        $this->isEditing = !$this->isEditing;
        if (!$this->isEditing) {
            // Reset value when canceling edit
            $attr = $this->attribute;
            $this->value = $this->model->$attr;
        }
    }

    #[On('reset-form')]
    public function resetForm($attrs){
        $this->value = $attrs[$this->attribute];
        $this->isEditing = false;
    }

    #[On('model-changed')]
    public function updateModel($model)
    {
        $this->model = $this->model->query()->findOrFail($model);
        $this->dispatch('reset-form', attrs: $this->model);
    }

    public function save(){
        $attr = $this->attribute;
        Validator::make([$this->attribute => $this->value], [$this->attribute => $this->rules])->validate();
        $this->model->$attr = $this->value;
        $this->model->save();
        $this->model = $this->model->refresh();
        $this->success('Successful', class_basename($this->model) .' updated', 'toast-top toast-center');
        $this->dispatch('model-updated');
        $this->isEditing = false;
    }
}; ?>

<div>
    @if(!$isEditing)
        <div class="flex flex-col">
            <span class="text-sm font-medium text-gray-600">{{ $label }}</span>
            <div class="flex items-center gap-2 group">
                <span class="text-gray-700">{{ $value ?: 'Empty' }}</span>
                <button wire:click="toggleEdit" type="button" class="opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500 hover:text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                </button>
            </div>
        </div>
    @else
        <div class="flex items-center gap-3">
            <div class="flex-1">
                <x-mary-textarea
                    type="{{ $type }}" 
                    wire:model="value"
                    label="{{ $label }}"
                    inline 
                    name="{{ $attribute }}" 
                    wire:keydown.enter="save"
                    wire:keydown.escape="toggleEdit"
                    autofocus
                />
            </div>
            <div class="flex gap-2">
                <button wire:click="save" type="button" class="px-3 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Save
                </button>
                <button wire:click="toggleEdit" type="button" class="px-3 py-2 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    Cancel
                </button>
            </div>
        </div>
        @if (session('status'))
            <div class="p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
                {{ session('status') }}
            </div>
        @endif
        <x-input-error :messages="$errors->get($attribute)" class="mt-2" />
    @endif
</div>