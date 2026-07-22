<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'DelDesk') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-stone-50 text-stone-900 antialiased">
        <main class="min-h-screen">
            <section class="bg-white">
                <div class="mx-auto flex min-h-[92vh] max-w-7xl flex-col px-6 py-6 lg:px-8">
                    <nav class="flex flex-wrap items-center justify-between gap-4">
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-3 text-teal-700">
                            <x-application-logo class="h-10 w-auto" />
                            <span class="text-lg font-semibold text-stone-950">DelDesk</span>
                        </a>

                        @if (Route::has('login'))
                            <div class="flex flex-wrap items-center gap-3 text-sm font-medium">
                                @auth
                                    <a href="{{ route('dashboard') }}" class="rounded-md border border-stone-300 bg-white px-4 py-2 text-stone-800 shadow-sm hover:border-teal-300 hover:text-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-600 focus:ring-offset-2">
                                        Dashboard
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="rounded-md border border-stone-300 bg-white px-4 py-2 text-stone-800 shadow-sm hover:border-teal-300 hover:text-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-600 focus:ring-offset-2">
                                        Log In
                                    </a>

                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="rounded-md bg-teal-700 px-4 py-2 text-white shadow-sm hover:bg-teal-800 focus:outline-none focus:ring-2 focus:ring-teal-600 focus:ring-offset-2">
                                            Register
                                        </a>
                                    @endif
                                @endauth
                            </div>
                        @endif
                    </nav>

                    <div class="flex flex-1 items-center py-14">
                        <div class="max-w-4xl">
                            <p class="text-sm font-semibold uppercase text-teal-700">IT Helpdesk and Asset Management</p>
                            <h1 class="mt-4 text-5xl font-semibold leading-tight text-stone-950 sm:text-6xl">
                                DelDesk
                            </h1>
                            <p class="mt-6 max-w-3xl text-lg leading-8 text-stone-600">
                                A Laravel MVP for reporting IT issues, assigning technicians, tracking status history, keeping ticket discussions in one place, and connecting incidents to managed assets.
                            </p>
                            <div class="mt-8 flex flex-wrap gap-3">
                                <a href="{{ route('login') }}" class="rounded-md bg-teal-700 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-teal-800 focus:outline-none focus:ring-2 focus:ring-teal-600 focus:ring-offset-2">
                                    Open DelDesk
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="rounded-md border border-stone-300 bg-white px-5 py-3 text-sm font-semibold text-stone-800 shadow-sm hover:border-teal-300 hover:text-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-600 focus:ring-offset-2">
                                        Create Requester Account
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="border-y border-stone-200 bg-stone-50">
                <div class="mx-auto grid max-w-7xl gap-6 px-6 py-10 md:grid-cols-4 lg:px-8">
                    @foreach ([
                        ['label' => 'Report', 'text' => 'Requesters submit issues with category, priority, location, asset, and attachments.'],
                        ['label' => 'Assign', 'text' => 'Administrators review open tickets and assign active technicians.'],
                        ['label' => 'Resolve', 'text' => 'Technicians start work, discuss progress, and record resolution notes.'],
                        ['label' => 'Close', 'text' => 'Requesters confirm resolution or reopen the ticket with a reason.'],
                    ] as $step)
                        <article class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
                            <h2 class="text-base font-semibold text-stone-950">{{ $step['label'] }}</h2>
                            <p class="mt-2 text-sm leading-6 text-stone-600">{{ $step['text'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="bg-white">
                <div class="mx-auto grid max-w-7xl gap-8 px-6 py-12 lg:grid-cols-2 lg:px-8">
                    <div>
                        <h2 class="text-2xl font-semibold text-stone-950">Core MVP Features</h2>
                        <ul class="mt-5 grid gap-3 text-sm leading-6 text-stone-700 sm:grid-cols-2">
                            <li>Role-based dashboards</li>
                            <li>Ticket CRUD and archive</li>
                            <li>Status workflow history</li>
                            <li>Comments and private attachments</li>
                            <li>Asset inventory and ticket links</li>
                            <li>Admin category and user management</li>
                        </ul>
                    </div>

                    <div>
                        <h2 class="text-2xl font-semibold text-stone-950">Roles</h2>
                        <div class="mt-5 grid gap-4 sm:grid-cols-3 lg:grid-cols-1">
                            <article class="rounded-lg border border-stone-200 bg-stone-50 p-4">
                                <h3 class="text-sm font-semibold text-stone-950">Administrator</h3>
                                <p class="mt-1 text-sm leading-6 text-stone-600">Manages users, categories, assets, assignments, and operational dashboards.</p>
                            </article>
                            <article class="rounded-lg border border-stone-200 bg-stone-50 p-4">
                                <h3 class="text-sm font-semibold text-stone-950">Technician</h3>
                                <p class="mt-1 text-sm leading-6 text-stone-600">Works assigned tickets, adds notes, uploads evidence, and resolves issues.</p>
                            </article>
                            <article class="rounded-lg border border-stone-200 bg-stone-50 p-4">
                                <h3 class="text-sm font-semibold text-stone-950">Requester</h3>
                                <p class="mt-1 text-sm leading-6 text-stone-600">Creates support tickets, tracks progress, comments, and confirms resolution.</p>
                            </article>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>
