<?php

use Mary\Traits\Toast;
use Livewire\Volt\Component;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\On;

new class extends Component
{
    use Toast;
    // use WithFileUploads;

    public Model $model;

    public string $attribute;

    public bool $value;

    public $rules;


    public function mount(Model $model)
    {
        $attr = $this->attribute;
        $this->value = $model->$attr;
    }

    #[On('reset-form')]
    public function resetForm($attrs){
        $this->value = $attrs[$this->attribute];
    }

    #[On('model-changed')]
    public function updateModel($model)
    {
        $this->model = $this->model->query()->findOrFail($model);
        $this->dispatch('reset-form', attrs: $this->model);
    }

    public function save()
    {
        // $this->authorize('update', $this->model); 
        $attr = $this->attribute;
        Validator::make([$this->attribute => $this->value], [$this->attribute => $this->rules])->validate();
        $this->model->$attr = $this->value;
        $this->model->save();
        $this->model = $this->model->refresh();
        $this->success('Successfull', class_basename($this->model) . ' updated', 'toast-top toast-center');
        $this->dispatch('model-updated');
    }
}
?>
<div class="p-3 border border-b rounded-md shadow-sm">
    <div class="flex items-center gap-3">
        <x-mary-checkbox wire:model="value" label="{{$this->attribute}}" inline name='{{$this->attribute}}' wire:keydown.enter='save' />
        <button wire:click="save" type="button" class="btn btn-outline btn-primary">Save</button>
    </div>
    @if (session('status'))
    <div class="p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
        {{ session('status') }}
    </div>
    @endif
    <x-input-error :messages="$errors->get($this->attribute)" class="mt-2" />
</div>