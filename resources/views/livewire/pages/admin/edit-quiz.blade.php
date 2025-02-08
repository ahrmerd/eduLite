<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Subject;
use App\Models\PastQuestionMaterial;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new #[Layout('components.layouts.admin')] class extends Component
{
    public Subject $subject;

    public function mount(Subject $subject)
    {
        $this->subject = $subject;
    }

}?>

<div class="">
    <div class="py-12">
    <livewire:admin.edit-quiz :subject="$subject"  />
        
    </div>
</div>