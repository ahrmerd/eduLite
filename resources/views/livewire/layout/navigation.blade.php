<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */

    public $navRoutes = [
        'dashboard' => 'Dashboard',
        'donate' => 'Donate',
        'quiz-dashboard' => 'Q & A',
        'past-questions' => 'Past Questions',
        'tutorials' => 'Tutorials'
    ];
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="bg-[#800000] text-white">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <!-- Logo -->
            <div class="shrink-0 flex items-center">
                <a href="{{ route('dashboard') }}" wire:navigate>
                    <x-application-logo class="block max-w-[80px] h-9 w-auto fill-current text-white" />
                </a>
            </div>

            <!-- Navigation Links -->
            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">

            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @foreach ($navRoutes as $route => $label)
                <x-nav-link :href="route($route)" :active="request()->routeIs($route)" wire:navigate class="text-white px-4 hover:text-gray-300 font-bold">
                    {{ $label }}
                </x-nav-link>
                @endforeach

                <x-mary-dropdown label="{{ auth()->user()->name}}" class=" border-0 text-white bg-[#800000] " right>
                    <x-mary-menu-item class="text-black" title="Profile" link="{{ route('profile') }}" />
                    <x-mary-menu-item class="text-red-800" wire:click="logout" title="Logout" />
                </x-mary-dropdown>

            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-gray-300 hover:bg-[#700000] focus:outline-none focus:bg-[#700000] focus:text-gray-300 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @foreach ($navRoutes as $route => $label)
            <x-responsive-nav-link
                :href="route($route)"
                :active="request()->routeIs($route)"
                wire:navigate
                class="text-white bg-[#800000] hover:bg-[#700000]">
                {{$label}}
            </x-responsive-nav-link>
            @endforeach
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t  border-[#700000]">
            <div class="px-4">
                <div class="font-medium text-base text-white" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                <div class="font-medium text-sm text-gray-300">{{ auth()->user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile')" wire:navigate class="text-white hover:bg-[#700000]">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link class="text-white hover:bg-[#700000]">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>