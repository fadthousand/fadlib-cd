<x-app-layout>
    <x-slot name="title">{{ $borrowing->transaction_code }}</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-mono text-slate">
                    <a href="{{ route('borrowings.index') }}" class="hover:text-navy underline">Circulation</a>
                    <span>/</span>
                    <span>{{ $borrowing->transaction_code }}</span>
                </div>
                <h1 class="font-serif text-3xl font-medium text-navy mt-1">Transaction Dossier</h1>
            </div>

            @if ($borrowing->status === 'borrowed')
                <form method="POST" action="{{ route('borrowings.return', $borrowing) }}" onsubmit="return confirm('Mark all volumes in this transaction as returned and restore stock?');">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-navy text-paper text-sm font-medium rounded-md hover:bg-slate transition">
                        Mark All Returned &amp; Restore Stock
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <aside class="bg-paper border border-silver rounded-lg p-6 h-fit">
            <p class="font-mono text-xs uppercase tracking-widest text-bronze">Ledger Metadata</p>

            <dl class="mt-4 divide-y divide-silver text-sm">
                <div class="py-3 flex justify-between gap-4">
                    <dt class="text-slate">Transaction Code</dt>
                    <dd class="font-mono font-medium text-navy">{{ $borrowing->transaction_code }}</dd>
                </div>
                <div class="py-3 flex justify-between gap-4">
                    <dt class="text-slate">Status</dt>
                    <dd>
                        @if ($borrowing->status === 'returned')
                            <span class="font-mono text-xs px-2 py-0.5 rounded bg-ivory border border-silver text-slate">Returned</span>
                        @elseif ($borrowing->isOverdue())
                            <span class="font-mono text-xs px-2 py-0.5 rounded bg-terracotta/10 border border-terracotta/40 text-terracotta">Overdue</span>
                        @else
                            <span class="font-mono text-xs px-2 py-0.5 rounded bg-sand/30 border border-bronze/40 text-navy">Borrowed</span>
                        @endif
                    </dd>
                </div>
                <div class="py-3 flex justify-between gap-4">
                    <dt class="text-slate">Member</dt>
                    <dd class="text-right">
                        <a href="{{ route('members.show', $borrowing->member) }}" class="font-medium text-navy hover:text-bronze underline">
                            {{ $borrowing->member->full_name }}
                        </a>
                        <div class="font-mono text-xs text-slate">{{ $borrowing->member->member_code }}</div>
                    </dd>
                </div>
                <div class="py-3 flex justify-between gap-4">
                    <dt class="text-slate">Borrow Date</dt>
                    <dd class="font-mono text-navy">{{ $borrowing->borrow_date->format('M d, Y') }}</dd>
                </div>
                <div class="py-3 flex justify-between gap-4">
                    <dt class="text-slate">Due Date</dt>
                    <dd class="font-mono {{ $borrowing->isOverdue() ? 'text-terracotta font-semibold' : 'text-navy' }}">
                        {{ $borrowing->due_date->format('M d, Y') }}
                    </dd>
                </div>
                <div class="py-3 flex justify-between gap-4">
                    <dt class="text-slate">Return Date</dt>
                    <dd class="font-mono text-navy">{{ $borrowing->return_date?->format('M d, Y') ?? 'Pending' }}</dd>
                </div>
                <div class="py-3 flex justify-between gap-4">
                    <dt class="text-slate">Processed By</dt>
                    <dd class="text-navy text-right">{{ $borrowing->operator?->name ?? 'Self-Service Reader' }}</dd>
                </div>
                @if ($borrowing->notes)
                    <div class="py-3">
                        <dt class="text-slate mb-1">Notes</dt>
                        <dd class="text-xs text-navy bg-ivory border border-silver rounded p-3">{{ $borrowing->notes }}</dd>
                    </div>
                @endif
            </dl>
        </aside>

        <section class="lg:col-span-2 bg-paper border border-silver rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-silver flex items-center justify-between">
                <div>
                    <h2 class="font-serif text-xl font-medium text-navy">Borrowed Volumes</h2>
                    <p class="text-xs text-slate">
                        {{ $borrowing->details->sum('quantity') }} total {{ Str::plural('copy', $borrowing->details->sum('quantity')) }} across {{ $borrowing->details->count() }} {{ Str::plural('title', $borrowing->details->count()) }}
                    </p>
                </div>
            </div>

            <div class="divide-y divide-silver">
                @foreach ($borrowing->details as $detail)
                    <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2.5">
                                <span class="font-mono text-xs font-medium px-2 py-0.5 rounded bg-ivory border border-silver text-navy">
                                    {{ $detail->book->volume_code }}
                                </span>
                                <a href="{{ route('books.show', $detail->book) }}" class="font-serif text-lg font-medium text-navy hover:text-bronze transition">
                                    {{ $detail->book->title }}
                                </a>
                            </div>
                            <p class="text-xs text-slate mt-1">
                                {{ $detail->book->category?->name }} &bull; {{ $detail->book->author }}
                            </p>
                        </div>

                        <div class="flex items-center gap-4 shrink-0">
                            <span class="font-mono text-xs px-2.5 py-1 rounded bg-sand/25 border border-bronze/40 text-navy">
                                Qty: {{ $detail->quantity }}
                            </span>
                            @if ($detail->book->epub_path)
                                <a href="{{ route('books.download', $detail->book) }}" class="text-xs font-medium text-navy hover:text-bronze underline">
                                    Download EPUB
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</x-app-layout>
