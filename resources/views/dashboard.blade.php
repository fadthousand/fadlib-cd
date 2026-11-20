<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="font-mono text-xs uppercase tracking-widest text-bronze">Overview</p>
                <h1 class="font-serif text-3xl font-medium text-navy mt-1">Dashboard</h1>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('borrowings.create') }}" class="inline-flex items-center px-4 py-2 bg-navy text-paper text-sm font-medium rounded-md hover:bg-slate transition">
                    + Record Borrowing
                </a>
                <a href="{{ route('books.create') }}" class="inline-flex items-center px-4 py-2 bg-ivory border border-silver text-navy text-sm font-medium rounded-md hover:bg-sand/20 transition">
                    + Add Book
                </a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-paper border border-silver rounded-lg p-5">
            <p class="font-mono text-xs uppercase tracking-wider text-slate">Total Books</p>
            <p class="font-serif text-3xl font-semibold text-navy mt-2">{{ number_format($totalTitles) }}</p>
            <p class="text-xs text-slate mt-2">Titles registered in the catalog</p>
        </div>

        <div class="bg-paper border border-silver rounded-lg p-5">
            <p class="font-mono text-xs uppercase tracking-wider text-slate">Available Stock</p>
            <p class="font-serif text-3xl font-semibold text-navy mt-2">{{ number_format($totalStock) }}</p>
            <p class="text-xs text-slate mt-2">Copies ready to borrow</p>
        </div>

        <div class="bg-paper border border-silver rounded-lg p-5">
            <p class="font-mono text-xs uppercase tracking-wider text-slate">Active Borrowings</p>
            <p class="font-serif text-3xl font-semibold text-navy mt-2">{{ number_format($activeLoansCount) }}</p>
            <p class="text-xs text-slate mt-2">{{ number_format($borrowedCopies) }} copies currently borrowed</p>
        </div>

        <div class="bg-paper border border-silver rounded-lg p-5">
            <p class="font-mono text-xs uppercase tracking-wider text-slate">Registered Members</p>
            <p class="font-serif text-3xl font-semibold text-navy mt-2">{{ number_format($totalMembers) }}</p>
            <p class="text-xs text-slate mt-2">Total library members</p>
        </div>
    </div>

    <div class="bg-paper border border-silver rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-silver flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="font-serif text-xl font-medium text-navy">Recent Borrowings</h2>
                <p class="text-xs text-slate">Latest borrowing and return transactions.</p>
            </div>
            <a href="{{ route('borrowings.index') }}" class="text-xs font-medium text-navy hover:text-bronze underline">
                View All &rarr;
            </a>
        </div>

        @if ($recentBorrowings->isEmpty())
            <div class="px-6 py-12 text-center">
                <p class="font-serif text-lg text-navy">No borrowing records yet.</p>
                <p class="text-sm text-slate mt-1">Create a new borrowing record for a registered member.</p>
                <div class="mt-4">
                    <a href="{{ route('borrowings.create') }}" class="inline-flex items-center px-4 py-2 bg-navy text-paper text-sm font-medium rounded-md hover:bg-slate transition">
                        Record Borrowing
                    </a>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-silver text-sm">
                    <thead class="bg-ivory">
                        <tr>
                            <th class="px-6 py-3 text-left font-mono text-xs uppercase tracking-wider text-slate">Transaction</th>
                            <th class="px-6 py-3 text-left font-mono text-xs uppercase tracking-wider text-slate">Member</th>
                            <th class="px-6 py-3 text-left font-mono text-xs uppercase tracking-wider text-slate">Books</th>
                            <th class="px-6 py-3 text-left font-mono text-xs uppercase tracking-wider text-slate">Due Date</th>
                            <th class="px-6 py-3 text-left font-mono text-xs uppercase tracking-wider text-slate">Status</th>
                            <th class="px-6 py-3 text-right font-mono text-xs uppercase tracking-wider text-slate">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-silver bg-paper">
                        @foreach ($recentBorrowings as $loan)
                            <tr class="hover:bg-ivory/60 transition">
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    <div class="font-mono text-xs font-medium text-navy">{{ $loan->transaction_code }}</div>
                                    <div class="text-xs text-slate">{{ $loan->borrow_date->format('M d, Y') }}</div>
                                </td>
                                <td class="px-6 py-3.5">
                                    <div class="font-medium text-navy">{{ $loan->member->full_name }}</div>
                                    <div class="font-mono text-xs text-slate">{{ $loan->member->member_code }}</div>
                                </td>
                                <td class="px-6 py-3.5 text-slate">
                                    {{ $loan->details->sum('quantity') }} {{ Str::plural('copy', $loan->details->sum('quantity')) }}
                                    ({{ $loan->details->count() }} {{ Str::plural('title', $loan->details->count()) }})
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap font-mono text-xs {{ $loan->isOverdue() ? 'text-terracotta font-semibold' : 'text-slate' }}">
                                    {{ $loan->due_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    @if ($loan->status === 'returned')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-mono bg-ivory border border-silver text-slate">
                                            Returned
                                        </span>
                                    @elseif ($loan->isOverdue())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-mono bg-terracotta/10 border border-terracotta/40 text-terracotta">
                                            Overdue
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-mono bg-sand/30 border border-bronze/40 text-navy">
                                            Borrowed
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-right">
                                    <a href="{{ route('borrowings.show', $loan) }}" class="text-xs font-medium text-navy hover:text-bronze underline">
                                        Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
