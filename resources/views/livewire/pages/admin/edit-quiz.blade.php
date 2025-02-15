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
} ?>

<div class="">
    <div class="py-12">
        <!-- Breadcrumbs -->
        <nav class="mb-6">
            <ol class="flex items-center space-x-2 text-sm text-gray-500">
                <li>
                    <a wire:navigate href="{{ route('admin.dashboard') }}" class="hover:text-gray-700">
                        <x-mary-icon name="o-home" class="w-4 h-4" />
                    </a>
                </li>
                <li>
                    <x-mary-icon name="o-chevron-right" class="w-4 h-4" />
                </li>
                <li>
                    <a wire:navigate href="{{ route('admin.subjects') }}" class="hover:text-gray-700">Subjects</a>
                </li>
                <li>
                    <x-mary-icon name="o-chevron-right" class="w-4 h-4" />
                </li>
                <li class="text-gray-900 font-medium">{{ $subject->name }}</li>
            </ol>
        </nav>

        <livewire:admin.edit-quiz :subject="$subject" />

    </div>
</div>