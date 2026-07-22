<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase text-teal-700">{{ $user->role }}</p>
                <h1 class="text-2xl font-semibold text-stone-950">{{ $dashboard['title'] }}</h1>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <section class="mb-8">
                <p class="max-w-3xl text-sm leading-6 text-stone-600">
                    {{ $dashboard['subtitle'] }}
                </p>
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

            <section class="mt-8 rounded-lg border border-stone-200 bg-white p-6 shadow-sm">
                <h2 class="text-base font-semibold text-stone-950">Recent Tickets</h2>
                @include('dashboard.partials.recent-tickets', [
                    'tickets' => $dashboard['recentTickets'],
                    'showRequester' => $dashboard['showRequester'],
                ])
            </section>

            @if ($dashboard['role'] === App\Models\User::ROLE_ADMIN)
                @include('dashboard.partials.admin-breakdowns', [
                    'categoryBreakdown' => $dashboard['categoryBreakdown'],
                    'priorityBreakdown' => $dashboard['priorityBreakdown'],
                ])
            @endif
        </div>
    </div>
</x-app-layout>
