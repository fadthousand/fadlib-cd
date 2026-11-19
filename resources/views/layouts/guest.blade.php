<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Fad Library') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-ivory text-navy font-sans antialiased flex flex-col justify-center items-center px-4 py-10 selection:bg-sand/50">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <a href="{{ route('login') }}" class="inline-flex flex-col items-center">
                    <span class="font-mono text-[11px] uppercase tracking-[0.22em] text-bronze mb-2">
                        Literary Anthology &amp; Ledger
                    </span>
                    <span class="font-serif text-3xl font-semibold tracking-tight text-navy">
                        Fad Library
                    </span>
                </a>
            </div>

            <div class="bg-paper border border-silver rounded-lg shadow-sm px-6 py-7 sm:px-8">
                {{ $slot }}
            </div>

            <p class="text-center text-xs text-slate mt-6 font-mono">
                Official EPUB Digital Archive System
            </p>
        </div>
    </body>
</html>
