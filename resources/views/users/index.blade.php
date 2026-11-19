<x-app-layout>
    <x-slot name="title">User Accounts</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="font-mono text-xs uppercase tracking-widest text-bronze">Access Control</p>
                <h1 class="font-serif text-3xl font-medium text-navy mt-1">System User Accounts</h1>
            </div>

            <a href="{{ route('users.create') }}" class="inline-flex items-center px-4 py-2 bg-navy text-paper text-sm font-medium rounded-md hover:bg-slate transition">
                + Create Account
            </a>
        </div>
    </x-slot>

    <form method="GET" action="{{ route('users.index') }}" class="bg-paper border border-silver rounded-lg p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Search by name, email, or role (admin, staff, member)..."
                class="flex-1 rounded-md border-silver bg-ivory/70 text-sm text-navy placeholder-mist focus:border-navy focus:bg-paper focus:ring-navy"
            >
            <div class="flex items-center gap-2">
                <button type="submit" class="inline-flex justify-center items-center px-4 py-2 bg-navy text-paper text-sm font-medium rounded-md hover:bg-slate transition">
                    Search
                </button>
                @if ($search !== '')
                    <a href="{{ route('users.index') }}" class="inline-flex justify-center items-center px-3 py-2 bg-ivory border border-silver text-xs font-medium text-slate hover:text-navy rounded-md transition">
                        Reset
                    </a>
                @endif
            </div>
        </div>
    </form>

    <div class="bg-paper border border-silver rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-silver text-sm">
                <thead class="bg-ivory">
                    <tr>
                        <th class="px-6 py-3 text-left font-mono text-xs uppercase tracking-wider text-slate">Name</th>
                        <th class="px-6 py-3 text-left font-mono text-xs uppercase tracking-wider text-slate">Email</th>
                        <th class="px-6 py-3 text-left font-mono text-xs uppercase tracking-wider text-slate">Role</th>
                        <th class="px-6 py-3 text-left font-mono text-xs uppercase tracking-wider text-slate">Created</th>
                        <th class="px-6 py-3 text-right font-mono text-xs uppercase tracking-wider text-slate">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-silver bg-paper">
                    @foreach ($users as $account)
                        <tr class="hover:bg-ivory/60 transition">
                            <td class="px-6 py-4 font-medium text-navy">
                                {{ $account->name }}
                                @if (Auth::id() === $account->id)
                                    <span class="ml-1.5 font-mono text-[10px] uppercase px-1.5 py-0.5 rounded bg-ivory border border-silver text-slate">You</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate">{{ $account->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-mono text-xs uppercase px-2.5 py-0.5 rounded {{ $account->role === 'admin' ? 'bg-navy text-paper' : ($account->role === 'staff' ? 'bg-sand/30 border border-bronze/40 text-navy' : 'bg-ivory border border-silver text-slate') }}">
                                    {{ $account->role }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-xs text-slate">
                                {{ $account->created_at?->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="inline-flex items-center gap-3">
                                    <a href="{{ route('users.edit', $account) }}" class="text-xs font-medium text-navy hover:text-bronze underline">
                                        Edit
                                    </a>
                                    @if (Auth::id() !== $account->id)
                                        <form method="POST" action="{{ route('users.destroy', $account) }}" onsubmit="return confirm('Delete this user account?');" class="inline">
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

        @if ($users->hasPages())
            <div class="px-6 py-4 border-t border-silver bg-ivory/40">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
