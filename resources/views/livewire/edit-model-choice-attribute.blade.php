<?php

use Illuminate\Database\Eloquent\Collection;
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
    public array $searchFields = [];
    public $attributeModel;
    public $attributePk;

    public Collection|array $options;


    public function search(string $value = '')
    {
        // Besides the search results, you must include on demand selected option
        $selectedOption = $this->attributeModel::query()->where($this->attributePk, $this->value)->first();
        // dump($this->attributeModel::query()->where('id', $this->value)->toRawSql());
        // dd($selectedOption);
        $queried = $this->attributeModel::query()
            // ->where('name', 'like', "%$value%")
            ->orWhere('pid', 'like', "%$value%")
            ->orWhere('first_name', 'like', "%$value%")
            ->orWhere('last_name', 'like', "%$value%")
            ->orWhere('middle_name', 'like', "%$value%")
            ->take(5)
            ->orderBy('pid')
            ->get();
        if($selectedOption){
            // dd($selectedOption);
            $queried->merge([$selectedOption]);
        }
        // dump($selectedOption);
        $this->options=$queried;
    }



    public string $attribute;

    public $value = '';

    public $rules;
    public $label;
    public $optionLabel;
    public $optionValue;
    public $optionSubLabel;


    public function mount(Model $model)
    {
        $attr = $this->attribute;
        $this->value = $model->$attr;
        $this->search();
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
        <x-mary-choices 
            label="{{$this->label ?? $this->attribute}}" 
            inline 
            debounce="300ms" 
            name='{{$this->attribute}}' 
            wire:keydown.enter='save'
            :options="$options"
            single
            searchable
            wire:model="value"
            :option-label="$optionLabel??'name'"
            :option-value="$optionValue??'id'"
            :option-sub-label="$optionSubLabel??''"
            placeholder="Select {{$this->label ?? $this->attribute}}"
            name='{{$this->attribute}}'
             />
        <button wire:click="save" type="button" class="btn btn-outline btn-primary">Save</button>
    </div>
    @if (session('status'))
    <div class="p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
        {{ session('status') }}
    </div>
    @endif
    <x-input-error :messages="$errors->get($this->attribute)" class="mt-2" />
</div>