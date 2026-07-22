<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'deskIT') }}</title>

        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('branding/deskit-mark-192.png') }}">
        <meta name="application-name" content="{{ config('app.name', 'deskIT') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="overflow-x-hidden font-sans antialiased text-slate-900">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-50">
            @include('layouts.navigation')

            <div class="lg:pl-72">
                <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur">
                    <div class="px-4 py-4 sm:px-6 lg:px-8">
                        <div class="flex items-start gap-3">
                            <button
                                type="button"
                                class="mt-1 inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 shadow-sm hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 lg:hidden"
                                x-on:click="sidebarOpen = true"
                                x-bind:aria-expanded="sidebarOpen.toString()"
                                aria-controls="mobile-sidebar"
                                aria-label="Open navigation menu"
                            >
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16" />
                                </svg>
                            </button>

                            <div class="min-w-0 flex-1">
                                @isset($header)
                                    {{ $header }}
                                @else
                                    <x-page-header title="deskIT" description="IT support workspace" />
                                @endisset
                            </div>
                        </div>
                    </div>
                </header>

                <main class="px-4 py-6 sm:px-6 lg:px-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
