@php
    $user = Auth::user();
@endphp

<x-app-layout>
    <x-slot name="title">{{ $book->volume_code }} — {{ $book->title }}</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-mono text-slate">
                    <a href="{{ route('books.index') }}" class="hover:text-navy underline">Collection</a>
                    <span>/</span>
                    <a href="{{ route('books.index', ['category' => $book->category_id]) }}" class="hover:text-navy underline">
                        {{ $book->category?->name ?? 'Series' }}
                    </a>
                </div>
                <div class="flex flex-wrap items-baseline gap-3 mt-2">
                    <span class="font-mono text-sm font-medium px-2.5 py-0.5 rounded bg-sand/30 border border-bronze/40 text-navy">
                        {{ $book->volume_code }}
                    </span>
                    <h1 class="font-serif text-3xl font-medium text-navy">{{ $book->title }}</h1>
                </div>
            </div>

            @if ($user?->isStaffOrAdmin())
                <div class="flex flex-wrap items-center gap-2.5">
                    <a href="{{ route('books.edit', $book) }}" class="inline-flex items-center px-4 py-2 bg-ivory border border-silver text-navy text-sm font-medium rounded-md hover:bg-sand/20 transition">
                        Edit Volume
                    </a>

                    <form method="POST" action="{{ route('books.destroy', $book) }}" onsubmit="return confirm('Remove this volume permanently from the archive?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-terracotta/10 border border-terracotta/40 text-terracotta text-sm font-medium rounded-md hover:bg-terracotta hover:text-paper transition">
                            Delete
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <article class="bg-paper border border-silver rounded-lg p-6 sm:p-8">
                <p class="font-mono text-xs uppercase tracking-widest text-bronze">Publisher Synopsis</p>

                @if ($book->synopsis)
                    <div class="font-serif text-base text-navy leading-relaxed mt-4 space-y-4">
                        <p>{{ $book->synopsis }}</p>
                    </div>
                @else
                    <p class="text-sm italic text-slate mt-4">
                        No synopsis metadata is recorded for this edition.
                    </p>
                @endif

                @if ($book->curator_note)
                    <div class="mt-8 pt-6 border-t border-silver">
                        <p class="font-mono text-xs uppercase tracking-widest text-slate">Curator Note</p>
                        <p class="text-sm text-navy mt-2 leading-relaxed bg-ivory border border-silver rounded-md p-4">
                            {{ $book->curator_note }}
                        </p>
                    </div>
                @endif
            </article>
        </div>

        <aside class="space-y-6">
            <div class="bg-paper border border-silver rounded-lg p-6">
                <p class="font-mono text-xs uppercase tracking-widest text-bronze">Bibliographic Record</p>

                <dl class="mt-4 divide-y divide-silver text-sm">
                    <div class="py-3 flex justify-between gap-4">
                        <dt class="text-slate">Series</dt>
                        <dd class="font-medium text-navy text-right">{{ $book->category?->name ?? '—' }}</dd>
                    </div>
                    <div class="py-3 flex justify-between gap-4">
                        <dt class="text-slate">Author</dt>
                        <dd class="font-medium text-navy text-right">{{ $book->author }}</dd>
                    </div>
                    <div class="py-3 flex justify-between gap-4">
                        <dt class="text-slate">Publication Year</dt>
                        <dd class="font-mono text-navy">{{ $book->publication_year ?? 'Unlisted' }}</dd>
                    </div>
                    <div class="py-3 flex justify-between gap-4">
                        <dt class="text-slate">Edition</dt>
                        <dd class="font-mono text-xs text-navy">Official English EPUB</dd>
                    </div>
                    <div class="py-3 flex justify-between gap-4">
                        <dt class="text-slate">Archive Stock</dt>
                        <dd class="font-mono font-medium {{ $book->stock > 0 ? 'text-navy' : 'text-terracotta' }}">
                            {{ $book->stock }} / {{ $book->total_copies }} copies
                        </dd>
                    </div>
                </dl>

                <div class="mt-6 pt-5 border-t border-silver space-y-3">
                    @if ($user?->isStaffOrAdmin())
                        @if ($book->epub_path)
                            <a href="{{ route('books.download', $book) }}" data-turbo="false" class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-navy text-paper text-sm font-medium rounded-md hover:bg-slate transition">
                                Download Official EPUB
                            </a>
                        @else
                            <p class="text-xs text-center font-mono text-slate py-2 bg-ivory border border-silver rounded">
                                No EPUB file attached
                            </p>
                        @endif

                        <a href="{{ route('borrowings.create') }}" class="w-full inline-flex justify-center items-center px-4 py-2 bg-ivory border border-silver text-navy text-sm font-medium rounded-md hover:bg-sand/20 transition">
                            Issue Loan to Member
                        </a>
                    @elseif ($user?->isMember())
                        @if ($activeMemberBorrowing)
                            <div class="rounded-md bg-sand/25 border border-bronze/40 p-3 text-xs text-navy">
                                <p class="font-medium">Active Loan on Your Shelf</p>
                                <p class="font-mono text-[11px] text-slate mt-0.5">
                                    Due {{ $activeMemberBorrowing->due_date->format('M d, Y') }}
                                </p>
                            </div>

                            @if ($book->epub_path)
                                <a href="{{ route('books.download', $book) }}" data-turbo="false" class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-navy text-paper text-sm font-medium rounded-md hover:bg-slate transition">
                                    Download Official EPUB
                                </a>
                            @endif

                            <a href="{{ route('shelf.index') }}" class="w-full inline-flex justify-center items-center px-4 py-2 bg-ivory border border-silver text-navy text-xs font-medium rounded-md hover:bg-sand/20 transition">
                                Manage on My Shelf
                            </a>
                        @elseif ($book->stock > 0)
                            <form method="POST" action="{{ route('books.checkout', $book) }}">
                                @csrf
                                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-navy text-paper text-sm font-medium rounded-md hover:bg-slate transition">
                                    Borrow Volume (7 Days)
                                </button>
                            </form>
                            <p class="text-[11px] text-slate text-center">
                                Borrowing unlocks direct EPUB download on your shelf.
                            </p>
                        @else
                            <button type="button" disabled class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-ivory border border-silver text-mist text-sm font-medium rounded-md cursor-not-allowed">
                                Currently Checked Out
                            </button>
                        @endif
                    @endif
                </div>
            </div>
        </aside>
    </div>
</x-app-layout>
