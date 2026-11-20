<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title . ' — ' : '' }}{{ config('app.name', 'Fad Library') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Newsreader:ital,opsz,wght@0,6..72,400;0,6..72,500;0,6..72,600;1,6..72,400&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen flex flex-col bg-ivory text-navy font-sans antialiased selection:bg-sand/50 selection:text-navy">
        @include('layouts.navigation')

        @isset($header)
            <header class="border-b border-silver bg-paper/70">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main class="flex-1 max-w-6xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @if (session('status'))
                <div class="mb-6 rounded-md border border-bronze/40 bg-sand/25 px-4 py-3 text-sm text-navy flex items-center justify-between gap-4">
                    <span>{{ session('status') }}</span>
                    <span class="font-mono text-xs uppercase tracking-wider text-bronze">Notice</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 rounded-md border border-terracotta/40 bg-terracotta/10 px-4 py-3 text-sm text-terracotta flex items-center justify-between gap-4">
                    <span>{{ session('error') }}</span>
                    <span class="font-mono text-xs uppercase tracking-wider">Alert</span>
                </div>
            @endif

            {{ $slot }}
        </main>

        <footer class="border-t border-silver bg-paper/70 py-6 text-xs text-slate">
            <div class="mx-auto flex max-w-6xl flex-col items-center justify-center gap-3 px-4 text-center sm:flex-row sm:justify-between sm:px-6 sm:text-left lg:px-8">
                <p class="font-serif italic text-navy">
                    Fad Library — Personal Digital Library &amp; Circulation System
                </p>
                <p class="font-mono text-[11px] uppercase tracking-wider text-slate">
                    Verified EPUB Archive
                </p>
            </div>
        </footer>
    </body>
</html>
