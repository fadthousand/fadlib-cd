<x-guest-layout>
    <div class="mb-6">
        <h1 class="font-serif text-2xl font-medium text-navy">Sign In</h1>
        <p class="text-sm text-slate mt-1">Enter your credentials to access the library ledger.</p>
    </div>

    <x-auth-session-status class="mb-4 text-sm text-bronze" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" value="Email Address" />
            <x-text-input id="email" class="block mt-1.5 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div>
            <x-input-label for="password" value="Password" />
            <x-text-input id="password" class="block mt-1.5 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-silver text-navy shadow-xs focus:ring-navy" name="remember">
                <span class="ms-2 text-xs text-slate">Remember session</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-xs text-slate hover:text-navy underline" href="{{ route('password.request') }}">
                    Forgot password?
                </a>
            @endif
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full py-2.5">
                Enter Archive
            </x-primary-button>
        </div>

        <div class="pt-4 border-t border-silver text-center">
            <p class="text-xs text-slate">
                New reader?
                <a href="{{ route('register') }}" class="font-medium text-navy hover:text-bronze underline">
                    Create a member account
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
