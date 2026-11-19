<x-app-layout>
    <x-slot name="title">My Shelf</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="font-mono text-xs uppercase tracking-widest text-bronze">Personal Reading Shelf</p>
                <h1 class="font-serif text-3xl font-medium text-navy mt-1">My Shelf</h1>
                @if ($member)
                    <p class="font-mono text-xs text-slate mt-1">
                        Member ID: {{ $member->member_code }} &bull; Status: {{ strtoupper($member->status) }}
                    </p>
                @endif
            </div>

            <a href="{{ route('books.index') }}" class="inline-flex items-center px-4 py-2 bg-navy text-paper text-sm font-medium rounded-md hover:bg-slate transition">
                Browse Collection
            </a>
        </div>
    </x-slot>

    @if (! $member)
        <div class="bg-paper border border-silver rounded-lg p-8 text-center">
            <p class="font-serif text-xl text-navy">No member profile is linked to your account.</p>
            <p class="text-sm text-slate mt-1">Please contact the library desk to link your reader profile.</p>
        </div>
    @else
        <div class="space-y-8">
            <section class="bg-paper border border-silver rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-silver bg-ivory/50 flex items-center justify-between">
                    <div>
                        <h2 class="font-serif text-xl font-medium text-navy">Active Loans</h2>
                        <p class="text-xs text-slate">Active loans unlock direct EPUB file downloads.</p>
                    </div>
                    <span class="font-mono text-xs text-bronze">{{ $activeBorrowings->count() }} Active</span>
                </div>

                @if ($activeBorrowings->isEmpty())
                    <div class="px-6 py-12 text-center">
                        <p class="font-serif text-lg text-navy">Your active shelf is empty.</p>
                        <p class="text-sm text-slate mt-1">Borrow any available volume from the collection to download its EPUB edition.</p>
                    </div>
                @else
                    <div class="divide-y divide-silver">
                        @foreach ($activeBorrowings as $loan)
                            <div class="p-6 space-y-4">
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <div class="flex flex-wrap items-center gap-2.5">
                                        <span class="font-mono text-xs font-medium px-2 py-0.5 rounded bg-ivory border border-silver text-navy">
                                            {{ $loan->transaction_code }}
                                        </span>
                                        <span class="font-mono text-xs {{ $loan->isOverdue() ? 'text-terracotta font-semibold' : 'text-slate' }}">
                                            Due {{ $loan->due_date->format('M d, Y') }}
                                        </span>
                                    </div>

                                    <form method="POST" action="{{ route('shelf.return', $loan) }}" onsubmit="return confirm('Return this loan and release your EPUB access?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-ivory border border-silver rounded text-xs font-medium text-navy hover:bg-sand/20 transition">
                                            Return Loan
                                        </button>
                                    </form>
                                </div>

                                <div class="grid grid-cols-1 gap-3">
                                    @foreach ($loan->details as $detail)
                                        <div class="bg-ivory/60 border border-silver rounded-md p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="font-mono text-xs text-slate">{{ $detail->book->volume_code }}</span>
                                                    <a href="{{ route('books.show', $detail->book) }}" class="font-serif text-lg font-medium text-navy hover:text-bronze transition">
                                                        {{ $detail->book->title }}
                                                    </a>
                                                </div>
                                                <p class="text-xs text-slate mt-0.5">
                                                    {{ $detail->book->category?->name }} &bull; {{ $detail->book->author }}
                                                </p>
                                            </div>

                                            @if ($detail->book->epub_path)
                                                <a href="{{ route('books.download', $detail->book) }}" class="inline-flex justify-center items-center px-4 py-2 bg-navy text-paper text-xs font-medium rounded-md hover:bg-slate transition shrink-0">
                                                    Download EPUB
                                                </a>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            <section class="bg-paper border border-silver rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-silver bg-ivory/50">
                    <h2 class="font-serif text-xl font-medium text-navy">Returned History</h2>
                    <p class="text-xs text-slate">Previously borrowed and returned volumes.</p>
                </div>

                @if ($returnedBorrowings->isEmpty())
                    <div class="px-6 py-8 text-center text-sm text-slate">
                        No past loans recorded yet.
                    </div>
                @else
                    <div class="divide-y divide-silver">
                        @foreach ($returnedBorrowings as $loan)
                            <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs">
                                <div>
                                    <span class="font-mono font-medium text-navy">{{ $loan->transaction_code }}</span>
                                    <span class="text-slate ml-2">
                                        {{ $loan->details->map(fn ($d) => "{$d->book->volume_code} ({$d->book->title})")->join(', ') }}
                                    </span>
                                </div>
                                <span class="font-mono text-slate shrink-0">
                                    Returned {{ $loan->return_date?->format('M d, Y') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    @endif
</x-app-layout>
