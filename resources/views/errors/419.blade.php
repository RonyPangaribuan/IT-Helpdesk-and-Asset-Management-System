@php
    $homeUrl = auth()->check() ? route('dashboard') : route('login');
    $homeLabel = auth()->check() ? 'Back to Dashboard' : 'Back to Login';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Session Expired - {{ config('app.name', 'deskIT') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-50 font-sans text-slate-900 antialiased">
        <main class="flex min-h-screen items-center justify-center px-4 py-12">
            <section class="w-full max-w-lg rounded-lg border border-slate-200 bg-white p-8 text-center shadow-sm">
                <p class="text-sm font-semibold text-indigo-700">deskIT</p>
                <h1 class="mt-3 text-4xl font-semibold text-slate-950">419</h1>
                <p class="mt-3 text-lg font-medium text-slate-900">Session expired</p>
                <p class="mt-2 text-sm leading-6 text-slate-600">Please refresh the form and submit it again.</p>
                <a href="{{ $homeUrl }}" class="app-button-primary mt-6">
                    {{ $homeLabel }}
                </a>
            </section>
        </main>
    </body>
</html>
