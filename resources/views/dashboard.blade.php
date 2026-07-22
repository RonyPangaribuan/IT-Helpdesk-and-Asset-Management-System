<x-app-layout>
    <x-slot name="header">
        <x-page-header
            :eyebrow="ucfirst($user->role)"
            :title="$dashboard['title']"
            :description="$dashboard['subtitle']"
        >
            <x-slot name="actions">
                @if ($dashboard['role'] === App\Models\User::ROLE_REQUESTER)
                    <a href="{{ route('tickets.create') }}" class="app-button-primary">Create Ticket</a>
                @elseif ($dashboard['role'] === App\Models\User::ROLE_TECHNICIAN)
                    <a href="{{ route('tickets.index', ['status' => App\Enums\TicketStatus::Assigned->value]) }}" class="app-button-secondary">Assigned</a>
                    <a href="{{ route('tickets.index', ['status' => App\Enums\TicketStatus::InProgress->value]) }}" class="app-button-primary">In Progress</a>
                @else
                    <a href="{{ route('tickets.index', ['status' => App\Enums\TicketStatus::Open->value]) }}" class="app-button-secondary">View Open Tickets</a>
                    <a href="{{ route('admin.users.index') }}" class="app-button-primary">Manage Users</a>
                @endif
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6">
        <section class="app-card overflow-hidden">
            <div class="grid gap-6 p-5 sm:p-6 lg:grid-cols-[1.5fr_1fr]">
                <div>
                    <p class="text-sm font-medium text-indigo-700">Welcome back, {{ $user->name }}</p>
                    <h2 class="mt-2 text-xl font-semibold tracking-tight text-slate-950">
                        @if ($dashboard['role'] === App\Models\User::ROLE_ADMIN)
                            Keep the support queue moving.
                        @elseif ($dashboard['role'] === App\Models\User::ROLE_TECHNICIAN)
                            Focus on the tickets assigned to you.
                        @else
                            Track requests from report to resolution.
                        @endif
                    </h2>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                        @if ($dashboard['role'] === App\Models\User::ROLE_ADMIN)
                            Review open tickets, assign work, and keep users, categories, and assets tidy from one workspace.
                        @elseif ($dashboard['role'] === App\Models\User::ROLE_TECHNICIAN)
                            Start assigned work, update progress through comments and attachments, then resolve with a clear note.
                        @else
                            Create a support ticket, follow its status, discuss updates, and confirm when the issue is solved.
                        @endif
                    </p>
                </div>

                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <p class="text-sm font-semibold text-slate-950">
                        @if ($dashboard['role'] === App\Models\User::ROLE_ADMIN)
                            Admin quick actions
                        @elseif ($dashboard['role'] === App\Models\User::ROLE_TECHNICIAN)
                            Your workflow
                        @else
                            How your request is handled
                        @endif
                    </p>

                    @if ($dashboard['role'] === App\Models\User::ROLE_ADMIN)
                        <div class="mt-4 grid gap-2 sm:grid-cols-2 lg:grid-cols-1">
                            <a href="{{ route('tickets.index', ['status' => App\Enums\TicketStatus::Open->value]) }}" class="app-button-secondary">View Open Tickets</a>
                            <a href="{{ route('assets.create') }}" class="app-button-secondary">Add Asset</a>
                            <a href="{{ route('admin.ticket-categories.index') }}" class="app-button-secondary">Manage Categories</a>
                            <a href="{{ route('admin.users.index') }}" class="app-button-secondary">Manage Users</a>
                        </div>
                    @elseif ($dashboard['role'] === App\Models\User::ROLE_TECHNICIAN)
                        <div class="mt-4 grid grid-cols-3 gap-2 text-center text-xs font-medium text-slate-600">
                            <span class="rounded-lg border border-indigo-200 bg-white px-3 py-2 text-indigo-700">Assigned</span>
                            <span class="rounded-lg border border-amber-200 bg-white px-3 py-2 text-amber-700">Start Work</span>
                            <span class="rounded-lg border border-emerald-200 bg-white px-3 py-2 text-emerald-700">Resolve</span>
                        </div>
                    @else
                        <ol class="mt-4 grid gap-2 text-sm text-slate-700">
                            @foreach (['Submit a ticket', 'Admin assigns a technician', 'Technician works on the issue', 'You confirm or reopen the resolution'] as $step)
                                <li class="flex items-center gap-3 rounded-lg bg-white px-3 py-2 ring-1 ring-slate-200">
                                    <span class="h-2 w-2 rounded-full bg-indigo-500" aria-hidden="true"></span>
                                    {{ $step }}
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </div>
            </div>
        </section>

        <section @class([
            'grid gap-4 md:grid-cols-2',
            'xl:grid-cols-6' => $dashboard['role'] === App\Models\User::ROLE_ADMIN,
            'xl:grid-cols-4' => $dashboard['role'] !== App\Models\User::ROLE_ADMIN,
        ])>
            @foreach ($dashboard['cards'] as $card)
                @include('dashboard.partials.stat-card', ['card' => $card])
            @endforeach
        </section>

        <x-section-card title="Recent Tickets" description="Latest requests visible to your role.">
            @include('dashboard.partials.recent-tickets', [
                'tickets' => $dashboard['recentTickets'],
                'showRequester' => $dashboard['showRequester'],
            ])
        </x-section-card>

        @if ($dashboard['role'] === App\Models\User::ROLE_ADMIN)
            @include('dashboard.partials.admin-breakdowns', [
                'categoryBreakdown' => $dashboard['categoryBreakdown'],
                'priorityBreakdown' => $dashboard['priorityBreakdown'],
            ])
        @endif
    </div>
</x-app-layout>
