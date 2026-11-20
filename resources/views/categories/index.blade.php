<x-app-layout>
    <x-slot name="title">Series</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="font-mono text-xs uppercase tracking-widest text-bronze">Categories</p>
                <h1 class="font-serif text-3xl font-medium text-navy mt-1">Book Series</h1>
            </div>

            @if (Auth::user()?->isAdmin())
                <a href="{{ route('categories.create') }}" class="inline-flex items-center px-4 py-2 bg-navy text-paper text-sm font-medium rounded-md hover:bg-slate transition">
                    + New Series
                </a>
            @endif
        </div>
    </x-slot>

    <form method="GET" action="{{ route('categories.index') }}" class="bg-paper border border-silver rounded-lg p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Search series title or author..."
                class="flex-1 rounded-md border-silver bg-ivory/70 text-sm text-navy placeholder-mist focus:border-navy focus:bg-paper focus:ring-navy"
            >
            <div class="flex items-center gap-2">
                <button type="submit" class="inline-flex justify-center items-center px-4 py-2 bg-navy text-paper text-sm font-medium rounded-md hover:bg-slate transition">
                    Search
                </button>
                @if ($search !== '')
                    <a href="{{ route('categories.index') }}" class="inline-flex justify-center items-center px-3 py-2 bg-ivory border border-silver text-xs font-medium text-slate hover:text-navy rounded-md transition">
                        Reset
                    </a>
                @endif
            </div>
        </div>
    </form>

    <div class="bg-paper border border-silver rounded-lg overflow-hidden">
        @if ($categories->isEmpty())
            <div class="px-6 py-12 text-center">
                <p class="font-serif text-lg text-navy">No series categories found.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-silver text-sm">
                    <thead class="bg-ivory">
                        <tr>
                            <th class="px-6 py-3 text-left font-mono text-xs uppercase tracking-wider text-slate">Series &amp; Author</th>
                            <th class="px-6 py-3 text-left font-mono text-xs uppercase tracking-wider text-slate">Volumes</th>
                            <th class="px-6 py-3 text-left font-mono text-xs uppercase tracking-wider text-slate">Available Copies</th>
                            <th class="px-6 py-3 text-right font-mono text-xs uppercase tracking-wider text-slate">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-silver bg-paper">
                        @foreach ($categories as $category)
                            <tr class="hover:bg-ivory/60 transition">
                                <td class="px-6 py-4">
                                    <a href="{{ route('books.index', ['category' => $category->id]) }}" class="font-serif text-lg font-medium text-navy hover:text-bronze transition">
                                        {{ $category->name }}
                                    </a>
                                    <div class="text-xs text-slate mt-0.5">{{ $category->author }}</div>
                                    @if ($category->epigraph)
                                        <p class="font-serif italic text-xs text-slate mt-1">&ldquo;{{ $category->epigraph }}&rdquo;</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-mono text-xs text-navy">
                                    {{ $category->books_count }} {{ Str::plural('volume', $category->books_count) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-mono text-xs text-slate">
                                    {{ number_format((int) ($category->books_sum_stock ?? 0)) }} copies
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="inline-flex items-center gap-3">
                                        <a href="{{ route('books.index', ['category' => $category->id]) }}" class="text-xs font-medium text-navy hover:text-bronze underline">
                                            Browse
                                        </a>

                                        @if (Auth::user()?->isAdmin())
                                            <a href="{{ route('categories.edit', $category) }}" class="text-xs font-medium text-slate hover:text-navy underline">
                                                Edit
                                            </a>

                                            <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('Delete this series category?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs font-medium text-terracotta hover:underline">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
