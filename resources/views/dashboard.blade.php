<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase text-teal-700">{{ $user->role }}</p>
                <h1 class="text-2xl font-semibold text-stone-950">{{ $dashboard['title'] }}</h1>
            </div>
            <span class="inline-flex w-fit items-center rounded-md bg-amber-50 px-3 py-1 text-sm font-medium text-amber-800 ring-1 ring-inset ring-amber-200">
                Milestone 1 Foundation
            </span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="px-4 sm:px-0">
                <section class="mb-8">
                    <p class="max-w-3xl text-sm leading-6 text-stone-600">
                        {{ $dashboard['subtitle'] }}
                    </p>
                </section>

                <section class="grid gap-4 md:grid-cols-3">
                    @foreach ($dashboard['cards'] as $card)
                        <div class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
                            <p class="text-sm font-medium text-stone-500">{{ $card['label'] }}</p>
                            <p class="mt-3 text-2xl font-semibold text-stone-950">{{ $card['value'] }}</p>
                        </div>
                    @endforeach
                </section>

                <section class="mt-8 rounded-lg border border-stone-200 bg-white p-6 shadow-sm">
                    <h2 class="text-base font-semibold text-stone-950">Planned Role Workflow</h2>
                    <ul class="mt-4 grid gap-3 text-sm text-stone-700 md:grid-cols-3">
                        @foreach ($dashboard['next'] as $item)
                            <li class="rounded-md bg-stone-50 px-4 py-3 ring-1 ring-inset ring-stone-200">{{ $item }}</li>
                        @endforeach
                    </ul>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
