@php
    $homeUrl = auth()->check() ? route('dashboard') : route('login');
    $homeLabel = auth()->check() ? 'Back to Dashboard' : 'Back to Login';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Access Denied - {{ config('app.name', 'DelDesk') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-stone-50 font-sans text-stone-900 antialiased">
        <main class="flex min-h-screen items-center justify-center px-4 py-12">
            <section class="w-full max-w-lg rounded-lg border border-stone-200 bg-white p-8 text-center shadow-sm">
                <p class="text-sm font-semibold uppercase text-teal-700">DelDesk</p>
                <h1 class="mt-3 text-4xl font-semibold text-stone-950">403</h1>
                <p class="mt-3 text-lg font-medium text-stone-900">Access denied</p>
                <p class="mt-2 text-sm leading-6 text-stone-600">Your account does not have permission to open this page.</p>
                <a href="{{ $homeUrl }}" class="mt-6 inline-flex items-center rounded-md bg-teal-700 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-teal-800 focus:outline-none focus:ring-2 focus:ring-teal-600 focus:ring-offset-2">
                    {{ $homeLabel }}
                </a>
            </section>
        </main>
    </body>
</html>
