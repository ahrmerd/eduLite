<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? config('app.name', 'Edu Lite') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script> -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])


</head>

<body class="font-sans antialiased">

    {{-- The navbar with `sticky` and `full-width` --}}
    <x-nav sticky full-width>

        <x-slot:brand>
            {{-- Drawer toggle for "main-drawer" --}}
            <label for="main-drawer" class="mr-3 lg:hidden">
                <x-mary-icon name="o-bars-3" class="cursor-pointer" />
            </label>

            {{-- Brand --}}
            <div>
                <a href="/" class="flex ms-2 md:me-24">
                    <img src="logo.png" class=" h-14 me-3" alt="" />
                    <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">EduLite Admin</span>
                </a>
            </div>
        </x-slot:brand>

        <x-slot:actions>
            <x-mary-dropdown label="{{ auth()->user()->name}}" class=" border-0" right>
                <x-mary-menu-item class="text-black dark:text-white" title="Profile" link="{{ route('profile') }}" />
                <x-mary-menu-item class="text-red-800" wire:click="logout">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <x-mary-button label="Logout" icon="o-power" type='submit' class="btn-ghost btn-sm" responsive />
                    </form>

                    </x-mary-item>
            </x-mary-dropdown>
        </x-slot:actions>


    </x-nav>

    {{-- The main content with `full-width` --}}
    <x-main with-nav full-width>

        {{-- This is a sidebar that works also as a drawer on small screens --}}
        {{-- Notice the `main-drawer` reference here --}}
        <x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-200">

            @php
            $sections = [
            'dashboard' => ['icon' => 'lucide.layout-dashboard', 'label' => 'Dashboard', 'route' => 'admin.dashboard'],
            // 'users' => ['icon' => 'lucide-users', 'label' => 'Users', 'route' => 'admin.users'],
            'subjects' => ['icon' => 'lucide.graduation-cap', 'label' => 'Subjects', 'route' => 'admin.subjects'],
            'ScoreBoard' => ['icon' => 'o-folder', 'label' => 'Score Board', 'route' => 'admin.scoreboard'],
            // 'questions' => ['icon' => 'lucide-file-question', 'label' => 'Questions', 'route' => 'admin.questions'],
            'attempts' => ['icon' => 'lucide.clipboard-list', 'label' => 'Quiz Attempts', 'route' => 'admin.quiz-attempts'],
            'materials' => ['icon' => 'lucide.file-text', 'label' => 'Past Questions', 'route' => 'admin.materials'],
            'tutorials' => ['icon' => 'lucide.video', 'label' => 'Tutorials', 'route' => 'admin.tutorials'],
            // 'roles' => ['icon' => 'lucide-shield-check', 'label' => 'Roles', 'route' => 'admin.roles'],
            ];
            @endphp

            {{-- Activates the menu item when a route matches the `link` property --}}
            <x-mary-menu activate-by-route>
                @foreach($sections as $key => $section)
                <x-mary-menu-item title="{{ $section['label'] }}" icon="{{ $section['icon'] }}" link="{{ route($section['route']) }}" />
                @endforeach

            </x-mary-menu>
        </x-slot:sidebar>

        {{-- The `$slot` goes here --}}
        <x-slot:content class="min-h-screen">

            {{ $slot }}
        </x-slot:content>
        <x-slot:footer>
            <x-footer />
        </x-slot:footer>
    </x-main>

    {{-- TOAST area --}}
    <x-toast />
    <script defer>
        function scrollParentToChild(parent, child) {

            // Where is the parent on page
            var parentRect = parent.getBoundingClientRect();
            // What can you see?
            var parentViewableArea = {
                height: parent.clientHeight,
                width: parent.clientWidth
            };

            // Where is the child
            var childRect = child.getBoundingClientRect();
            // Is the child viewable?
            var isViewable = (childRect.top >= parentRect.top) && (childRect.bottom <= parentRect.top + parentViewableArea
                .height);

            // if you can't see the child try to scroll parent
            if (!isViewable) {
                // Should we scroll using top or bottom? Find the smaller ABS adjustment
                const scrollTop = childRect.top - parentRect.top;
                const scrollBot = childRect.bottom - parentRect.bottom;
                if (Math.abs(scrollTop) < Math.abs(scrollBot)) {
                    // we're near the top of the list
                    parent.scrollTop += scrollTop;
                } else {
                    // we're near the bottom of the list
                    parent.scrollTop += scrollBot + 30;
                }
            }

        }

        function scrollToActiveMenuItem() {
            // Find the active menu item by mary-active-menu attribute or class if applicable
            const activeMenuItem = document.querySelector(".mary-active-menu");
            const sidebar = document.querySelector('[drawer="main-drawer"]')
            // console.log(activeMenuItem);
            // console.log('sa');

            // Check if an active item is present and scroll it into view
            if (activeMenuItem && sidebar) {
                scrollParentToChild(sidebar, activeMenuItem)
                // sidebar
                // sidebarContent.scrollTo({
                //     top: 200,
                //     behavior: "smooth"
                // });
                // activeMenuItem.scrollIntoView({
                //     behavior: "smooth",
                //     block: "center"
                // });
            }
        }
        document.addEventListener("livewire:initialized", scrollToActiveMenuItem);
        // document.addEventListener("livewire:init", scrollToActiveMenuItem);

        // Trigger scroll every time Livewire updates the DOM
        document.addEventListener("livewire:navigated", scrollToActiveMenuItem);
    </script>


</body>

</html>