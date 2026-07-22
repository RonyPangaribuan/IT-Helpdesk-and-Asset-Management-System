<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'DelDesk') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="min-h-screen bg-slate-50">
            <div class="grid min-h-screen lg:grid-cols-[0.95fr_1.05fr]">
                <section class="hidden bg-slate-950 px-10 py-10 text-white lg:flex lg:flex-col lg:justify-between">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-3 text-white">
                        <x-application-logo class="h-10 w-auto" />
                    </a>

                    <div class="max-w-md">
                        <p class="text-sm font-medium text-indigo-200">IT Helpdesk and Asset Management</p>
                        <h1 class="mt-4 text-3xl font-semibold tracking-tight">Support work, organized from login onward.</h1>
                        <p class="mt-4 text-sm leading-6 text-slate-300">
                            DelDesk keeps reports, assignments, comments, attachments, and asset history in one role-aware workspace.
                        </p>
                    </div>

                    <p class="text-xs text-slate-400">Laravel monolith / Blade / Tailwind CSS</p>
                </section>

                <main class="flex min-h-screen flex-col justify-center px-4 py-8 sm:px-6 lg:px-12">
                    <div class="mx-auto w-full max-w-md">
                        <div class="mb-8 lg:hidden">
                            <a href="{{ route('home') }}" class="inline-flex items-center gap-3 text-indigo-700">
                                <x-application-logo class="h-10 w-auto" />
                            </a>
                        </div>

                        <div class="app-card p-6 sm:p-8">
                            {{ $slot }}
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
