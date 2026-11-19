@php
    $user = Auth::user();
@endphp

<nav x-data="{ open: false }" class="bg-paper border-b border-silver sticky top-0 z-30">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-8">
                <a href="{{ $user && $user->isStaffOrAdmin() ? route('dashboard') : route('books.index') }}" class="flex items-baseline gap-2.5 group">
                    <span class="font-serif text-xl font-semibold tracking-tight text-navy group-hover:text-slate transition">
                        Fad Library
                    </span>
                    <span class="font-mono text-[11px] uppercase tracking-widest text-bronze border border-bronze/40 bg-sand/20 px-1.5 py-0.5 rounded">
                        Archive
                    </span>
                </a>

                <div class="hidden md:flex items-center gap-1">
                    @if ($user && $user->isStaffOrAdmin())
                        <a href="{{ route('dashboard') }}"
                           class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-navy text-paper' : 'text-slate hover:text-navy hover:bg-ivory' }}">
                            Dashboard
                        </a>
                    @endif

                    <a href="{{ route('books.index') }}"
                       class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('books.*') ? 'bg-navy text-paper' : 'text-slate hover:text-navy hover:bg-ivory' }}">
                        Collection
                    </a>

                    <a href="{{ route('categories.index') }}"
                       class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('categories.*') ? 'bg-navy text-paper' : 'text-slate hover:text-navy hover:bg-ivory' }}">
                        Series
                    </a>

                    @if ($user && $user->isMember())
                        <a href="{{ route('shelf.index') }}"
                           class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('shelf.*') ? 'bg-navy text-paper' : 'text-slate hover:text-navy hover:bg-ivory' }}">
                            My Shelf
                        </a>
                    @endif

                    @if ($user && $user->isStaffOrAdmin())
                        <a href="{{ route('members.index') }}"
                           class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('members.*') ? 'bg-navy text-paper' : 'text-slate hover:text-navy hover:bg-ivory' }}">
                            Members
                        </a>

                        <a href="{{ route('borrowings.index') }}"
                           class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('borrowings.*') ? 'bg-navy text-paper' : 'text-slate hover:text-navy hover:bg-ivory' }}">
                            Circulation
                        </a>
                    @endif

                    @if ($user && $user->isAdmin())
                        <a href="{{ route('users.index') }}"
                           class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('users.*') ? 'bg-navy text-paper' : 'text-slate hover:text-navy hover:bg-ivory' }}">
                            Users
                        </a>
                    @endif
                </div>
            </div>

            <div class="hidden md:flex items-center gap-4">
                @if ($user)
                    <div class="flex items-center gap-2.5 text-sm">
                        <a href="{{ route('profile.edit') }}" class="text-navy font-medium hover:text-slate transition">
                            {{ $user->name }}
                        </a>
                        <span class="font-mono text-[11px] uppercase tracking-wider px-2 py-0.5 rounded border border-silver bg-ivory text-slate">
                            {{ $user->role }}
                        </span>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-xs font-medium text-slate hover:text-terracotta transition px-2.5 py-1.5 rounded border border-silver hover:border-terracotta/40 bg-ivory">
                            Log Out
                        </button>
                    </form>
                @endif
            </div>

            <div class="flex items-center md:hidden">
                <button @click="open = ! open" type="button" class="p-2 rounded-md text-slate hover:text-navy hover:bg-ivory focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden md:hidden border-t border-silver bg-paper px-4 pt-3 pb-4 space-y-1">
        @if ($user && $user->isStaffOrAdmin())
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-navy text-paper' : 'text-slate' }}">Dashboard</a>
        @endif
        <a href="{{ route('books.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('books.*') ? 'bg-navy text-paper' : 'text-slate' }}">Collection</a>
        <a href="{{ route('categories.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('categories.*') ? 'bg-navy text-paper' : 'text-slate' }}">Series</a>
        @if ($user && $user->isMember())
            <a href="{{ route('shelf.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('shelf.*') ? 'bg-navy text-paper' : 'text-slate' }}">My Shelf</a>
        @endif
        @if ($user && $user->isStaffOrAdmin())
            <a href="{{ route('members.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('members.*') ? 'bg-navy text-paper' : 'text-slate' }}">Members</a>
            <a href="{{ route('borrowings.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('borrowings.*') ? 'bg-navy text-paper' : 'text-slate' }}">Circulation</a>
        @endif
        @if ($user && $user->isAdmin())
            <a href="{{ route('users.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('users.*') ? 'bg-navy text-paper' : 'text-slate' }}">Users</a>
        @endif

        @if ($user)
            <div class="pt-3 mt-3 border-t border-silver flex items-center justify-between">
                <div>
                    <div class="text-sm font-medium text-navy">{{ $user->name }}</div>
                    <div class="font-mono text-xs text-slate uppercase">{{ $user->role }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs font-medium text-terracotta border border-terracotta/40 px-3 py-1.5 rounded">
                        Log Out
                    </button>
                </form>
            </div>
        @endif
    </div>
</nav>
