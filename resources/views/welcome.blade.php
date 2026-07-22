<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'deskIT') }}</title>
        <meta name="description" content="deskIT centralizes support requests, technician workflows, collaboration, and asset history for IT teams.">
        <meta name="application-name" content="{{ config('app.name', 'deskIT') }}">
        <meta name="theme-color" content="#4338ca">

        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('branding/deskit-mark-192.png') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-50 text-slate-900 antialiased">
        <main class="min-h-screen">
            <section class="bg-white">
                <div class="mx-auto flex min-h-[86vh] max-w-7xl flex-col px-6 py-6 lg:px-8">
                    <nav class="flex flex-wrap items-center justify-between gap-4" aria-label="Public navigation">
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-3 text-indigo-700">
                            <x-application-logo class="h-10 w-auto" />
                        </a>

                        @if (Route::has('login'))
                            <div class="flex flex-wrap items-center gap-3 text-sm font-medium">
                                @auth
                                    <a href="{{ route('dashboard') }}" class="app-button-secondary">
                                        Dashboard
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="app-button-secondary">
                                        Log In
                                    </a>

                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="app-button-primary">
                                            Create Requester Account
                                        </a>
                                    @endif
                                @endauth
                            </div>
                        @endif
                    </nav>

                    <div class="grid flex-1 items-center gap-10 py-12 lg:grid-cols-[0.95fr_0.75fr]">
                        <div class="max-w-3xl">
                            <p class="text-sm font-semibold text-indigo-700">IT Helpdesk & Asset Management</p>
                            <h1 class="mt-4 text-4xl font-semibold leading-tight tracking-tight text-slate-950 sm:text-6xl">
                                IT support, organized from report to resolution.
                            </h1>
                            <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-600">
                                deskIT centralizes support requests, technician workflows, collaboration, and asset history in one clear Laravel system.
                            </p>
                            <div class="mt-8 flex flex-wrap gap-3">
                                <a href="{{ route('login') }}" class="app-button-primary px-5 py-3">
                                    Open deskIT
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="app-button-secondary px-5 py-3">
                                        Create Requester Account
                                    </a>
                                @endif
                            </div>
                        </div>

                        <figure class="mx-auto w-full max-w-sm lg:max-w-md">
                            <img
                                src="{{ asset('branding/deskit-logo-full.png') }}"
                                alt="deskIT support workflow illustration with ticket, headset, devices, and check mark"
                                width="770"
                                height="892"
                                class="max-h-[420px] w-full object-contain"
                            >
                        </figure>
                    </div>
                </div>
            </section>

            <section class="border-y border-slate-200 bg-slate-50">
                <div class="mx-auto grid max-w-7xl gap-6 px-6 py-12 md:grid-cols-3 lg:px-8">
                    @foreach ([
                        ['title' => 'Centralized reports', 'text' => 'Requests no longer disappear into chat threads or spreadsheets.'],
                        ['title' => 'Role-aware workflow', 'text' => 'Requester, technician, and administrator screens focus on their next action.'],
                        ['title' => 'Asset context', 'text' => 'Tickets can connect issues with the device history technicians need.'],
                    ] as $feature)
                        <article class="app-card p-5">
                            <h2 class="text-base font-semibold text-slate-950">{{ $feature['title'] }}</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ $feature['text'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="bg-white">
                <div class="mx-auto grid max-w-7xl gap-8 px-6 py-14 lg:grid-cols-[0.8fr_1.2fr] lg:px-8">
                    <div>
                        <p class="text-sm font-semibold text-indigo-700">Role overview</p>
                        <h2 class="mt-2 text-2xl font-semibold tracking-tight text-slate-950">Clear workspaces for each user type.</h2>
                    </div>

                    <div class="grid gap-4 md:grid-cols-3">
                        @foreach ([
                            ['title' => 'Requester', 'text' => 'Create tickets, track progress, comment, upload evidence, and confirm resolution.'],
                            ['title' => 'Technician', 'text' => 'View assigned tickets, start work, collaborate, upload files, and resolve issues.'],
                            ['title' => 'Administrator', 'text' => 'Manage users, assign tickets, organize categories, maintain assets, and review operations.'],
                        ] as $role)
                            <article class="rounded-lg border border-slate-200 bg-slate-50 p-5">
                                <h3 class="text-sm font-semibold text-slate-950">{{ $role['title'] }}</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-600">{{ $role['text'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <footer class="border-t border-slate-200 bg-white">
                <div class="mx-auto flex max-w-7xl flex-col gap-3 px-6 py-6 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between lg:px-8">
                    <p>deskIT MVP / Laravel monolith / Blade and Tailwind CSS</p>
                    <a href="{{ route('login') }}" class="app-link">Open deskIT</a>
                </div>
            </footer>
        </main>
    </body>
</html>
