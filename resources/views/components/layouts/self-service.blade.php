<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? config('app.name', 'Work & Persons') }}</title>

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
                    <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">Work &
                        Persons</span>
                </a>
            </div>
        </x-slot:brand>

        <x-slot:actions>
            <div class="flex flex-col">
                <p>{{ auth('personnel')->user()->fullname }}</p>
                <p class="text-xs">{{ auth('personnel')->user()->email }}</p>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <x-mary-button label="Logout" icon="o-power" type='submit' class="btn-ghost btn-sm" responsive />
            </form>
        </x-slot:actions>


    </x-nav>

    {{-- The main content with `full-width` --}}
    <x-main with-nav full-width>

        {{-- This is a sidebar that works also as a drawer on small screens --}}
        {{-- Notice the `main-drawer` reference here --}}
        <x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-200">

            

            {{-- Activates the menu item when a route matches the `link` property --}}
            <x-mary-menu activate-by-route>
                <x-mary-menu-item title="Personal Dashboard" icon="o-user-group" link="{{ route('personnel-dashboard') }}" />
                <x-mary-menu-item title="Payslips" icon="o-banknotes" link="{{ route('selfservice.payslips') }}" />
                <x-mary-menu-item title="Contacts" icon="o-phone" link="{{ route('personnels.contacts') }}" />
                <x-mary-menu-item title="Referees" icon="o-user-circle" link="{{ route('personnels.referees') }}" />
                <x-mary-menu-item title="Next of kins" icon="o-users" link="{{route('personnels.next-of-kin')}}" />
                <x-mary-menu-item title="O Levels" icon="o-academic-cap" link="{{route('personnels.o-levels')}}" />
                <x-mary-menu-item title="Certifications" icon="o-document-check" link="{{route('personnels.certifications')}}" />
                <x-mary-menu-item title="Memberships" icon="o-identification" link="{{route('personnels.memberships')}}" />
            </x-mary-menu>
        </x-slot:sidebar>

        {{-- The `$slot` goes here --}}
        <x-slot:content class="min-h-screen">

            {{ $slot }}
        </x-slot:content>
        <x-slot:footer class="py-8 text-white bg-gray-800">
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