<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Fad Library') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Newsreader:ital,opsz,wght@0,6..72,400;0,6..72,500;0,6..72,600;1,6..72,400&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

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
