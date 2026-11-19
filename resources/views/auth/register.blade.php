<x-guest-layout>
    <div class="mb-6">
        <h1 class="font-serif text-2xl font-medium text-navy">Reader Registration</h1>
        <p class="text-sm text-slate mt-1">Register a reader profile to borrow and download EPUB editions.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="name" value="Full Name" />
            <x-text-input id="name" class="block mt-1.5 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
        </div>

        <div>
            <x-input-label for="email" value="Email Address" />
            <x-text-input id="email" class="block mt-1.5 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div>
            <x-input-label for="password" value="Password" />
            <x-text-input id="password" class="block mt-1.5 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Confirm Password" />
            <x-text-input id="password_confirmation" class="block mt-1.5 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full py-2.5">
                Register Reader Account
            </x-primary-button>
        </div>

        <div class="pt-4 border-t border-silver text-center">
            <p class="text-xs text-slate">
                Already registered?
                <a href="{{ route('login') }}" class="font-medium text-navy hover:text-bronze underline">
                    Sign in to archive
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
