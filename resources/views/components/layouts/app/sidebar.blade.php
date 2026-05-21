<aside :class="{ 'w-full md:w-64': sidebarOpen, 'w-0 md:w-16 hidden md:block': !sidebarOpen }"
    class="bg-sidebar text-sidebar-foreground border-r border-gray-200 dark:border-gray-700 sidebar-transition overflow-hidden">
    <!-- Sidebar Content -->
    <div class="h-full flex flex-col">
        <!-- Sidebar Menu -->
        <nav class="flex-1 overflow-y-auto custom-scrollbar py-4">
            <ul class="space-y-1 px-2">
                <!-- Dashboard -->
                <x-layouts.sidebar-link href="{{ route('dashboard') }}" icon='fas-house'
                    :active="request()->routeIs('dashboard*')">Dashboard</x-layouts.sidebar-link>

                @if(in_array(auth()->user()?->role, ['admin', 'employee'], true))
                <x-layouts.sidebar-link href="{{ route('users.index') }}" icon='fas-users'
                    :active="request()->routeIs('users.*')">Gebruikers</x-layouts.sidebar-link>

                <x-layouts.sidebar-link href="{{ route('employees.index') }}" icon='fas-user-tie'
                    :active="request()->routeIs('employees.*')">Medewerkers</x-layouts.sidebar-link>

                <x-layouts.sidebar-link href="{{ route('visitors.index') }}" icon='fas-id-card'
                    :active="request()->routeIs('visitors.*')">Bezoekers</x-layouts.sidebar-link>

                <x-layouts.sidebar-link href="{{ route('visits.index') }}" icon='fas-calendar-check'
                    :active="request()->routeIs('visits.*')">Bezoeken</x-layouts.sidebar-link>

                <x-layouts.sidebar-link href="{{ route('departments.index') }}" icon='fas-building'
                    :active="request()->routeIs('departments.*')">Afdelingen</x-layouts.sidebar-link>

                    <x-layouts.sidebar-link href="{{ route('mailbox.index') }}" icon='fas-inbox'
                        :active="request()->routeIs('mailbox.*')">Mailbox</x-layouts.sidebar-link>

                    <x-layouts.sidebar-link href="{{ route('notifications.index') }}" icon='fas-bell'
                        :active="request()->routeIs('notifications.*')">Notificaties</x-layouts.sidebar-link>
                @endif
            </ul>
        </nav>
    </div>
</aside>