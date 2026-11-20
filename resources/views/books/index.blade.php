<x-app-layout>
    <x-slot name="title">Collection</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="font-mono text-xs uppercase tracking-widest text-bronze">Book Catalog</p>
                <h1 class="font-serif text-3xl font-medium text-navy mt-1">Book Collection</h1>
                <p class="text-sm text-slate mt-1">
                    Showing {{ number_format($totalVolumes) }} {{ Str::plural('volume', $totalVolumes) }} across {{ number_format($seriesList->count()) }} {{ Str::plural('series', $seriesList->count()) }}.
                </p>
            </div>

            @if (Auth::user()?->isStaffOrAdmin())
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('books.create') }}" class="inline-flex items-center px-4 py-2 bg-navy text-paper text-sm font-medium rounded-md hover:bg-slate transition">
                        + Add Book
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    <form method="GET" action="{{ route('books.index') }}" class="bg-paper border border-silver rounded-lg p-4 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
            <div class="md:col-span-6">
                <label for="search" class="block font-mono text-[11px] uppercase tracking-wider text-slate mb-1.5">
                    Search Volume Code or Author
                </label>
                <input
                    id="search"
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="e.g. COTE, SSW, Makoto Shinkai..."
                    class="w-full rounded-md border-silver bg-ivory/70 text-sm text-navy placeholder-mist focus:border-navy focus:bg-paper focus:ring-navy"
                >
            </div>

            <div class="md:col-span-4">
                <label for="category" class="block font-mono text-[11px] uppercase tracking-wider text-slate mb-1.5">
                    Filter by Series
                </label>
                <select
                    id="category"
                    name="category"
                    class="w-full rounded-md border-silver bg-ivory/70 text-sm text-navy focus:border-navy focus:bg-paper focus:ring-navy"
                >
                    <option value="">All Series</option>
                    @foreach ($allCategories as $cat)
                        <option value="{{ $cat->id }}" @selected($selectedCategoryId === $cat->id)>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2 flex items-center gap-2">
                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-navy text-paper text-sm font-medium rounded-md hover:bg-slate transition">
                    Filter
                </button>
                @if ($search !== '' || $selectedCategoryId > 0)
                    <a href="{{ route('books.index') }}" class="inline-flex justify-center items-center px-3 py-2 bg-ivory border border-silver text-xs font-medium text-slate hover:text-navy rounded-md transition">
                        Reset
                    </a>
                @endif
            </div>
        </div>
    </form>

    @if ($seriesList->isEmpty())
        <div class="bg-paper border border-silver rounded-lg px-6 py-14 text-center">
            <p class="font-serif text-xl text-navy">No volumes match your current filter.</p>
            <p class="text-sm text-slate mt-1">Try clearing the search query or selecting another series.</p>
            <div class="mt-5">
                <a href="{{ route('books.index') }}" class="inline-flex items-center px-4 py-2 bg-ivory border border-silver text-sm font-medium text-navy rounded-md hover:bg-sand/20 transition">
                    Show All Volumes
                </a>
            </div>
        </div>
    @else
        <div class="space-y-10">
            @foreach ($seriesList as $series)
                <section class="bg-paper border border-silver rounded-lg overflow-hidden">
                    <div class="px-6 py-5 border-b border-silver bg-ivory/50 flex flex-col md:flex-row md:items-baseline md:justify-between gap-2">
                        <div>
                            <div class="flex flex-wrap items-baseline gap-x-3 gap-y-1">
                                <h2 class="font-serif text-2xl font-medium text-navy">{{ $series->name }}</h2>
                                <span class="text-sm text-slate">by {{ $series->author }}</span>
                            </div>

                            @if ($series->epigraph)
                                <p class="font-serif italic text-sm text-slate mt-2 max-w-3xl">
                                    &ldquo;{{ $series->epigraph }}&rdquo;
                                </p>
                            @endif

                            @if ($series->description)
                                <p class="text-xs text-slate mt-1.5 max-w-3xl leading-relaxed">
                                    {{ $series->description }}
                                </p>
                            @endif
                        </div>

                        <div class="shrink-0 font-mono text-xs text-bronze uppercase tracking-wider">
                            {{ $series->books->count() }} {{ Str::plural('Volume', $series->books->count()) }}
                        </div>
                    </div>

                    <div class="divide-y divide-silver">
                        @foreach ($series->books as $book)
                            <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 hover:bg-ivory/50 transition">
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2.5">
                                        <span class="font-mono text-xs font-medium text-navy bg-ivory border border-silver px-2 py-0.5 rounded">
                                            {{ $book->volume_code }}
                                        </span>

                                        <a href="{{ route('books.show', $book) }}" class="font-serif text-lg font-medium text-navy hover:text-bronze transition">
                                            {{ $book->title }}
                                        </a>

                                        @if ($book->publication_year)
                                            <span class="font-mono text-xs text-slate">({{ $book->publication_year }})</span>
                                        @endif
                                    </div>

                                    @if ($book->synopsis)
                                        <p class="text-xs text-slate mt-1.5 line-clamp-2 max-w-3xl leading-relaxed">
                                            {{ $book->synopsis }}
                                        </p>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between sm:justify-end gap-4 shrink-0">
                                    <div class="text-right">
                                        @if ($book->stock > 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded font-mono text-xs bg-sand/25 border border-bronze/40 text-navy">
                                                {{ $book->stock }} / {{ $book->total_copies }} available
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded font-mono text-xs bg-terracotta/10 border border-terracotta/40 text-terracotta">
                                                0 / {{ $book->total_copies }} checked out
                                            </span>
                                        @endif
                                    </div>

                                    <a href="{{ route('books.show', $book) }}" class="inline-flex items-center px-3 py-1.5 bg-ivory border border-silver rounded text-xs font-medium text-navy hover:bg-navy hover:text-paper hover:border-navy transition">
                                        Details
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    @endif
</x-app-layout>
