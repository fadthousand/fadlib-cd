<x-app-layout>
    <x-slot name="title">Circulation Ledger</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="font-mono text-xs uppercase tracking-widest text-bronze">Archive Circulation</p>
                <h1 class="font-serif text-3xl font-medium text-navy mt-1">Borrowing Ledger</h1>
            </div>

            <a href="{{ route('borrowings.create') }}" class="inline-flex items-center px-4 py-2 bg-navy text-paper text-sm font-medium rounded-md hover:bg-slate transition">
                + Record New Loan
            </a>
        </div>
    </x-slot>

    <form method="GET" action="{{ route('borrowings.index') }}" class="bg-paper border border-silver rounded-lg p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
            <div class="md:col-span-6">
                <label for="search" class="block font-mono text-[11px] uppercase tracking-wider text-slate mb-1.5">
                    Transaction Code or Member
                </label>
                <input
                    id="search"
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="e.g. BRW-2026, MEM-2026, Fad T..."
                    class="w-full rounded-md border-silver bg-ivory/70 text-sm text-navy placeholder-mist focus:border-navy focus:bg-paper focus:ring-navy"
                >
            </div>

            <div class="md:col-span-4">
                <label for="status" class="block font-mono text-[11px] uppercase tracking-wider text-slate mb-1.5">
                    Circulation Status
                </label>
                <select id="status" name="status" class="w-full rounded-md border-silver bg-ivory/70 text-sm text-navy focus:border-navy focus:bg-paper focus:ring-navy">
                    <option value="">All Records</option>
                    <option value="borrowed" @selected($status === 'borrowed')>Borrowed (Active)</option>
                    <option value="returned" @selected($status === 'returned')>Returned</option>
                </select>
            </div>

            <div class="md:col-span-2 flex items-center gap-2">
                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-navy text-paper text-sm font-medium rounded-md hover:bg-slate transition">
                    Filter
                </button>
                @if ($search !== '' || $status !== '')
                    <a href="{{ route('borrowings.index') }}" class="inline-flex justify-center items-center px-3 py-2 bg-ivory border border-silver text-xs font-medium text-slate hover:text-navy rounded-md transition">
                        Reset
                    </a>
                @endif
            </div>
        </div>
    </form>

    <div class="bg-paper border border-silver rounded-lg overflow-hidden">
        @if ($borrowings->isEmpty())
            <div class="px-6 py-12 text-center">
                <p class="font-serif text-lg text-navy">No circulation records match your filter.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-silver text-sm">
                    <thead class="bg-ivory">
                        <tr>
                            <th class="px-6 py-3 text-left font-mono text-xs uppercase tracking-wider text-slate">Transaction</th>
                            <th class="px-6 py-3 text-left font-mono text-xs uppercase tracking-wider text-slate">Member</th>
                            <th class="px-6 py-3 text-left font-mono text-xs uppercase tracking-wider text-slate">Items</th>
                            <th class="px-6 py-3 text-left font-mono text-xs uppercase tracking-wider text-slate">Loan Window</th>
                            <th class="px-6 py-3 text-left font-mono text-xs uppercase tracking-wider text-slate">Status</th>
                            <th class="px-6 py-3 text-right font-mono text-xs uppercase tracking-wider text-slate">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-silver bg-paper">
                        @foreach ($borrowings as $loan)
                            <tr class="hover:bg-ivory/60 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-mono text-xs font-medium text-navy">{{ $loan->transaction_code }}</div>
                                    <div class="text-xs text-slate">
                                        {{ $loan->operator ? 'By ' . $loan->operator->name : 'Self-service' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('members.show', $loan->member) }}" class="font-medium text-navy hover:text-bronze transition">
                                        {{ $loan->member->full_name }}
                                    </a>
                                    <div class="font-mono text-xs text-slate">{{ $loan->member->member_code }}</div>
                                </td>
                                <td class="px-6 py-4 text-xs text-navy">
                                    <div>{{ $loan->details->sum('quantity') }} {{ Str::plural('copy', $loan->details->sum('quantity')) }}</div>
                                    <div class="text-slate truncate max-w-xs">
                                        {{ $loan->details->pluck('book.volume_code')->join(', ') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-mono text-xs">
                                    <div class="text-slate">Out: {{ $loan->borrow_date->format('M d, Y') }}</div>
                                    <div class="{{ $loan->isOverdue() ? 'text-terracotta font-semibold' : 'text-navy' }}">
                                        Due: {{ $loan->due_date->format('M d, Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
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
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <a href="{{ route('borrowings.show', $loan) }}" class="text-xs font-medium text-navy hover:text-bronze underline">
                                        Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($borrowings->hasPages())
                <div class="px-6 py-4 border-t border-silver bg-ivory/40">
                    {{ $borrowings->links() }}
                </div>
            @endif
        @endif
    </div>
</x-app-layout>
