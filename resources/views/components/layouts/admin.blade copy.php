<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Top Navigation Bar -->
        <nav class="bg-white border-b border-gray-200">
            <div class="px-4 mx-auto max-w-full">
                <div class="flex justify-between h-16">
                    <!-- Logo / Brand -->
                    <div class="flex">
                        <div class="flex items-center shrink-0">
                            <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold text-gray-800">
                                {{ config('app.name') }} Admin
                            </a>
                        </div>
                    </div>

                    <!-- Right Side Nav Items -->
                    <div class="flex items-center">
                        <!-- Search -->
                        <div class="relative mx-4">
                            <input
                                type="text"
                                class="w-64 pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-blue-500"
                                placeholder="Search...">
                            <div class="absolute left-3 top-2.5">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>

                        <!-- Notifications -->
                        <button class="p-2 text-gray-600 hover:text-gray-900">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </button>

                        <!-- Profile Dropdown -->
                        <div class="ml-3 relative">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                        <div>{{ Auth::user()->name }}</div>

                                        <div class="ml-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link :href="route('profile')">
                                        {{ __('Profile') }}
                                    </x-dropdown-link>

                                    <!-- Authentication -->
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault();
                                                        this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main>
            <div class="flex bg-gray-100 min-h-screen">
                <!-- Sidebar -->
                <div class="w-64 bg-white shadow-lg">
                    <div class="p-4 border-b">
                        <h1 class="text-xl font-bold text-gray-800">Quiz Admin</h1>
                    </div>

                    @php
                    $sections = [
                    'dashboard' => ['icon' => 'lucide-layout-dashboard', 'label' => 'Dashboard', 'route' => 'admin.dashboard'],
                    // 'users' => ['icon' => 'lucide-users', 'label' => 'Users', 'route' => 'admin.users'],
                    'subjects' => ['icon' => 'lucide-graduation-cap', 'label' => 'Subjects', 'route' => 'admin.subjects'],
                    // 'questions' => ['icon' => 'lucide-file-question', 'label' => 'Questions', 'route' => 'admin.questions'],
                    'attempts' => ['icon' => 'lucide-clipboard-list', 'label' => 'Quiz Attempts', 'route' => 'admin.quiz-attempts'],
                    'materials' => ['icon' => 'lucide-file-text', 'label' => 'Past Questions', 'route' => 'admin.materials'],
                    'tutorials' => ['icon' => 'lucide-video', 'label' => 'Tutorials', 'route' => 'admin.tutorials'],
                    // 'roles' => ['icon' => 'lucide-shield-check', 'label' => 'Roles', 'route' => 'admin.roles'],
                    ];
                    @endphp

                    <nav class="p-4">
                        @foreach($sections as $key => $section)
                        <a
                            href="{{ route($section['route']) }}"
                            wire:navigate
                            class="flex items-center w-full p-2 rounded-lg mb-1
                    {{ request()->routeIs($section['route']) ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50' }}">
                            <x-dynamic-component :component="$section['icon']" class="w-5 h-5 mr-3" />
                            {{ $section['label'] }}
                        </a>
                        @endforeach
                    </nav>
                </div>
                    <div class="flex-1 p-5 m-3">
                        {{ $slot }}

                    </div>
            </div>
        </main>
        <x-mary-toast/>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200">
            <div class="max-w-full mx-auto py-4 px-4">
                <div class="text-center text-sm text-gray-500">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </div>
            </div>
        </footer>
    </div>

</body>

</html>