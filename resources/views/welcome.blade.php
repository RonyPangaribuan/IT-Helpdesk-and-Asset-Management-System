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
            <section class="mx-auto flex min-h-screen max-w-7xl flex-col px-6 py-8 lg:px-8">
                <nav class="flex items-center justify-between">
                    <a href="{{ url('/') }}" class="text-teal-700">
                        <x-application-logo class="h-10 w-auto" />
                    </a>

                    @if (Route::has('login'))
                        <div class="flex items-center gap-3 text-sm font-medium">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="rounded-md border border-stone-300 bg-white px-4 py-2 text-stone-800 shadow-sm hover:border-teal-300 hover:text-teal-700">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="rounded-md border border-stone-300 bg-white px-4 py-2 text-stone-800 shadow-sm hover:border-teal-300 hover:text-teal-700">
                                    Log In
                                </a>

                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="rounded-md bg-teal-700 px-4 py-2 text-white shadow-sm hover:bg-teal-800">
                                        Register
                                    </a>
                                @endif
                            @endauth
                        </div>
                    @endif
                </nav>

                <div class="grid flex-1 items-center gap-10 py-12 lg:grid-cols-2">
                    <section class="max-w-2xl">
                        <p class="text-sm font-semibold uppercase text-teal-700">IT Helpdesk MVP</p>
                        <h1 class="mt-4 text-4xl font-semibold leading-tight text-stone-950">
                            Report, assign, and track internal IT support work.
                        </h1>
                        <p class="mt-5 text-base leading-7 text-stone-600">
                            DelDesk starts with secure authentication and role-based access for administrators, technicians, and requesters. Ticket and asset workflows will be added through the planned milestones.
                        </p>
                    </section>

                    <section class="grid gap-4 sm:grid-cols-3 lg:grid-cols-1">
                        <div class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
                            <p class="text-sm font-medium text-stone-500">Admin</p>
                            <p class="mt-2 text-sm text-stone-700">Assign tickets, manage users, categories, and assets.</p>
                        </div>
                        <div class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
                            <p class="text-sm font-medium text-stone-500">Technician</p>
                            <p class="mt-2 text-sm text-stone-700">Handle assigned tickets and record resolution work.</p>
                        </div>
                        <div class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
                            <p class="text-sm font-medium text-stone-500">Requester</p>
                            <p class="mt-2 text-sm text-stone-700">Create tickets and monitor personal request progress.</p>
                        </div>
                    </section>
                </div>
            </section>
        </main>
    </body>
</html>
