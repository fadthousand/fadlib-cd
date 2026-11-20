<x-app-layout>
    <x-slot name="title">{{ $member->member_code }} — {{ $member->full_name }}</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-mono text-slate">
                    <a href="{{ route('members.index') }}" class="hover:text-navy underline">Members</a>
                    <span>/</span>
                    <span>{{ $member->member_code }}</span>
                </div>
                <h1 class="font-serif text-3xl font-medium text-navy mt-1">{{ $member->full_name }}</h1>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('members.edit', $member) }}" class="inline-flex items-center px-4 py-2 bg-ivory border border-silver text-navy text-sm font-medium rounded-md hover:bg-sand/20 transition">
                    Edit Profile
                </a>
                <a href="{{ route('borrowings.create') }}" class="inline-flex items-center px-4 py-2 bg-navy text-paper text-sm font-medium rounded-md hover:bg-slate transition">
                    + Record Borrowing
                </a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <aside class="bg-paper border border-silver rounded-lg p-6 h-fit">
            <p class="font-mono text-xs uppercase tracking-widest text-bronze">Member Profile</p>

            <dl class="mt-4 divide-y divide-silver text-sm">
                <div class="py-3 flex justify-between gap-4">
                    <dt class="text-slate">Member Code</dt>
                    <dd class="font-mono font-medium text-navy">{{ $member->member_code }}</dd>
                </div>
                <div class="py-3 flex justify-between gap-4">
                    <dt class="text-slate">Status</dt>
                    <dd class="font-mono text-xs uppercase {{ $member->status === 'active' ? 'text-navy' : 'text-terracotta' }}">
                        {{ $member->status }}
                    </dd>
                </div>
                <div class="py-3 flex justify-between gap-4">
                    <dt class="text-slate">Email</dt>
                    <dd class="text-navy text-right break-all">{{ $member->email }}</dd>
                </div>
                <div class="py-3 flex justify-between gap-4">
                    <dt class="text-slate">Phone</dt>
                    <dd class="font-mono text-navy text-right">{{ $member->phone ?? '—' }}</dd>
                </div>
                <div class="py-3 flex justify-between gap-4">
                    <dt class="text-slate">Login Account</dt>
                    <dd class="font-mono text-xs text-navy text-right">
                        {{ $member->user ? $member->user->email : 'Walk-in / Desk Only' }}
                    </dd>
                </div>
                <div class="py-3">
                    <dt class="text-slate mb-1">Address</dt>
                    <dd class="text-navy text-xs leading-relaxed">{{ $member->address ?? 'No address recorded.' }}</dd>
                </div>
            </dl>
        </aside>

        <section class="lg:col-span-2 bg-paper border border-silver rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-silver flex items-center justify-between">
                <div>
                    <h2 class="font-serif text-xl font-medium text-navy">Borrowing History</h2>
                    <p class="text-xs text-slate">{{ $borrowings->count() }} {{ Str::plural('transaction', $borrowings->count()) }} on record</p>
                </div>
            </div>

            @if ($borrowings->isEmpty())
                <div class="px-6 py-12 text-center">
                    <p class="font-serif text-lg text-navy">No borrowing history for this member.</p>
                </div>
            @else
                <div class="divide-y divide-silver">
                    @foreach ($borrowings as $loan)
                        <div class="p-6 hover:bg-ivory/40 transition">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-2.5">
                                    <a href="{{ route('borrowings.show', $loan) }}" class="font-mono text-xs font-medium text-navy hover:text-bronze underline">
                                        {{ $loan->transaction_code }}
                                    </a>
                                    <span class="text-xs text-slate">
                                        Borrowed {{ $loan->borrow_date->format('M d, Y') }} &bull; Due {{ $loan->due_date->format('M d, Y') }}
                                    </span>
                                </div>

                                @if ($loan->status === 'returned')
                                    <span class="font-mono text-xs px-2 py-0.5 rounded bg-ivory border border-silver text-slate">
                                        Returned {{ $loan->return_date?->format('M d, Y') }}
                                    </span>
                                @elseif ($loan->isOverdue())
                                    <span class="font-mono text-xs px-2 py-0.5 rounded bg-terracotta/10 border border-terracotta/40 text-terracotta">
                                        Overdue
                                    </span>
                                @else
                                    <span class="font-mono text-xs px-2 py-0.5 rounded bg-sand/30 border border-bronze/40 text-navy">
                                        Borrowed
                                    </span>
                                @endif
                            </div>

                            <ul class="mt-3 space-y-1.5 text-xs text-navy">
                                @foreach ($loan->details as $detail)
                                    <li class="flex items-center justify-between gap-2 bg-ivory/70 border border-silver/70 rounded px-3 py-1.5">
                                        <span>
                                            <span class="font-mono text-slate">{{ $detail->book->volume_code }}</span>
                                            — {{ $detail->book->category?->name }}: {{ $detail->book->title }}
                                        </span>
                                        <span class="font-mono text-slate">&times;{{ $detail->quantity }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
