<x-app-layout>
    <x-slot name="title">Members</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="font-mono text-xs uppercase tracking-widest text-bronze">Member Management</p>
                <h1 class="font-serif text-3xl font-medium text-navy mt-1">Members</h1>
            </div>

            <a href="{{ route('members.create') }}" class="inline-flex items-center px-4 py-2 bg-navy text-paper text-sm font-medium rounded-md hover:bg-slate transition">
                + Add Member
            </a>
        </div>
    </x-slot>

    <form method="GET" action="{{ route('members.index') }}" class="bg-paper border border-silver rounded-lg p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Search member code, full name, or email..."
                class="flex-1 rounded-md border-silver bg-ivory/70 text-sm text-navy placeholder-mist focus:border-navy focus:bg-paper focus:ring-navy"
            >
            <div class="flex items-center gap-2">
                <button type="submit" class="inline-flex justify-center items-center px-4 py-2 bg-navy text-paper text-sm font-medium rounded-md hover:bg-slate transition">
                    Search
                </button>
                @if ($search !== '')
                    <a href="{{ route('members.index') }}" class="inline-flex justify-center items-center px-3 py-2 bg-ivory border border-silver text-xs font-medium text-slate hover:text-navy rounded-md transition">
                        Reset
                    </a>
                @endif
            </div>
        </div>
    </form>

    <div class="bg-paper border border-silver rounded-lg overflow-hidden">
        @if ($members->isEmpty())
            <div class="px-6 py-12 text-center">
                <p class="font-serif text-lg text-navy">No member records found.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-silver text-sm">
                    <thead class="bg-ivory">
                        <tr>
                            <th class="px-6 py-3 text-left font-mono text-xs uppercase tracking-wider text-slate">Member Code</th>
                            <th class="px-6 py-3 text-left font-mono text-xs uppercase tracking-wider text-slate">Full Name &amp; Contact</th>
                            <th class="px-6 py-3 text-left font-mono text-xs uppercase tracking-wider text-slate">Active Loans</th>
                            <th class="px-6 py-3 text-left font-mono text-xs uppercase tracking-wider text-slate">Status</th>
                            <th class="px-6 py-3 text-right font-mono text-xs uppercase tracking-wider text-slate">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-silver bg-paper">
                        @foreach ($members as $member)
                            <tr class="hover:bg-ivory/60 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-mono text-xs font-medium px-2 py-0.5 rounded bg-ivory border border-silver text-navy">
                                        {{ $member->member_code }}
                                    </span>
                                    @if ($member->user)
                                        <div class="font-mono text-[11px] text-bronze mt-1">Linked Account</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('members.show', $member) }}" class="font-medium text-navy hover:text-bronze transition">
                                        {{ $member->full_name }}
                                    </a>
                                    <div class="text-xs text-slate">{{ $member->email }}</div>
                                    @if ($member->phone)
                                        <div class="font-mono text-xs text-slate mt-0.5">{{ $member->phone }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-mono text-xs text-navy">
                                    {{ $member->active_loans_count }} {{ Str::plural('loan', $member->active_loans_count) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($member->status === 'active')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-mono bg-sand/25 border border-bronze/40 text-navy">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-mono bg-terracotta/10 border border-terracotta/40 text-terracotta">
                                            Suspended
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="inline-flex items-center gap-3">
                                        <a href="{{ route('members.show', $member) }}" class="text-xs font-medium text-navy hover:text-bronze underline">
                                            Details
                                        </a>
                                        <a href="{{ route('members.edit', $member) }}" class="text-xs font-medium text-slate hover:text-navy underline">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('members.destroy', $member) }}" onsubmit="return confirm('Delete this member record?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-medium text-terracotta hover:underline">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($members->hasPages())
                <div class="px-6 py-4 border-t border-silver bg-ivory/40">
                    {{ $members->links() }}
                </div>
            @endif
        @endif
    </div>
</x-app-layout>
