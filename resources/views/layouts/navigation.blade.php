@php
    $user = Auth::user();
    $ticketActive = request()->routeIs('tickets.index') || request()->routeIs('tickets.show') || request()->routeIs('tickets.edit');

    $navigationSections = [
        [
            'label' => 'Workspace',
            'items' => [
                ['label' => 'Dashboard', 'href' => route('dashboard'), 'active' => request()->routeIs('dashboard*')],
                ['label' => $user->isRequester() ? 'My Tickets' : 'Tickets', 'href' => route('tickets.index'), 'active' => $ticketActive],
            ],
        ],
    ];

    if ($user->isRequester()) {
        $navigationSections[0]['items'][] = ['label' => 'Create Ticket', 'href' => route('tickets.create'), 'active' => request()->routeIs('tickets.create')];
    }

    if ($user->isAdmin() || $user->isTechnician()) {
        $navigationSections[0]['items'][] = ['label' => 'Assets', 'href' => route('assets.index'), 'active' => request()->routeIs('assets.*')];
    }

    if ($user->isAdmin()) {
        $navigationSections[] = [
            'label' => 'Management',
            'items' => [
                ['label' => 'Users', 'href' => route('admin.users.index'), 'active' => request()->routeIs('admin.users.*')],
                ['label' => 'Ticket Categories', 'href' => route('admin.ticket-categories.index'), 'active' => request()->routeIs('admin.ticket-categories.*')],
                ['label' => 'Asset Categories', 'href' => route('admin.asset-categories.index'), 'active' => request()->routeIs('admin.asset-categories.*')],
            ],
        ];
    }

    $navigationSections[] = [
        'label' => 'Account',
        'items' => [
            ['label' => 'Profile', 'href' => route('profile.edit'), 'active' => request()->routeIs('profile.edit')],
        ],
    ];
@endphp

<div
    id="mobile-sidebar"
    class="relative z-50 lg:hidden"
    x-show="sidebarOpen"
    x-on:keydown.escape.window="sidebarOpen = false"
    role="dialog"
    aria-modal="true"
    aria-label="Mobile navigation"
    style="display: none;"
>
    <div
        class="fixed inset-0 bg-slate-900/45"
        x-show="sidebarOpen"
        x-transition.opacity
        x-on:click="sidebarOpen = false"
        aria-hidden="true"
    ></div>

    <aside
        class="fixed inset-y-0 left-0 flex w-80 max-w-[calc(100vw-2rem)] flex-col border-r border-slate-200 bg-white shadow-xl"
        x-show="sidebarOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
    >
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-3 text-indigo-700">
                <x-application-logo class="h-9 w-auto" />
            </a>
            <button
                type="button"
                class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                x-on:click="sidebarOpen = false"
                aria-label="Close navigation menu"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <nav class="flex-1 space-y-7 overflow-y-auto px-4 py-5" aria-label="Primary mobile navigation">
            @foreach ($navigationSections as $section)
                <div>
                    <p class="px-3 text-xs font-semibold text-slate-400">{{ $section['label'] }}</p>
                    <div class="mt-2 space-y-1">
                        @foreach ($section['items'] as $item)
                            <x-sidebar-link :href="$item['href']" :active="$item['active']" x-on:click="sidebarOpen = false">
                                {{ $item['label'] }}
                            </x-sidebar-link>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>

        <div class="border-t border-slate-200 p-4">
            <div class="flex items-center gap-3">
                <x-avatar :user="$user" />
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold text-slate-950">{{ $user->name }}</p>
                    <p class="truncate text-xs text-slate-500">{{ $user->email }}</p>
                    <p class="mt-1 text-xs font-medium text-indigo-700">{{ ucfirst($user->role) }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="app-button-secondary w-full">Log Out</button>
            </form>
        </div>
    </aside>
</div>

<aside class="hidden lg:fixed lg:inset-y-0 lg:z-40 lg:flex lg:w-72 lg:flex-col lg:border-r lg:border-slate-200 lg:bg-white">
    <div class="flex h-full min-h-0 flex-col">
        <div class="border-b border-slate-200 px-6 py-5">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-3 text-indigo-700">
                <x-application-logo class="h-10 w-auto" />
            </a>
            <p class="mt-3 text-xs leading-5 text-slate-500">IT support workspace for report, assignment, repair, and asset history.</p>
        </div>

        <nav class="flex-1 space-y-7 overflow-y-auto px-4 py-6" aria-label="Primary navigation">
            @foreach ($navigationSections as $section)
                <div>
                    <p class="px-3 text-xs font-semibold text-slate-400">{{ $section['label'] }}</p>
                    <div class="mt-2 space-y-1">
                        @foreach ($section['items'] as $item)
                            <x-sidebar-link :href="$item['href']" :active="$item['active']">
                                {{ $item['label'] }}
                            </x-sidebar-link>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>

        <div class="border-t border-slate-200 p-4">
            <div class="flex items-center gap-3 rounded-lg bg-slate-50 p-3">
                <x-avatar :user="$user" />
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold text-slate-950">{{ $user->name }}</p>
                    <p class="truncate text-xs text-slate-500">{{ $user->email }}</p>
                    <p class="mt-1 text-xs font-medium text-indigo-700">{{ ucfirst($user->role) }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="app-button-secondary w-full">Log Out</button>
            </form>
        </div>
    </div>
</aside>
