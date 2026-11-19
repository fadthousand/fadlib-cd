<x-app-layout>
    <x-slot name="title">Edit User Account</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="font-mono text-xs uppercase tracking-widest text-bronze">Access Control</p>
                <h1 class="font-serif text-3xl font-medium text-navy mt-1">Edit User Account</h1>
            </div>
            <a href="{{ route('users.index') }}" class="text-sm text-slate hover:text-navy underline">
                &larr; Back to Users
            </a>
        </div>
    </x-slot>

    <div class="max-w-2xl bg-paper border border-silver rounded-lg p-6 sm:p-8">
        <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <x-input-label for="name" value="Display Name" />
                <x-text-input id="name" name="name" type="text" class="mt-1.5 block w-full" :value="old('name', $user->name)" required />
                <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <x-input-label for="email" value="Email Address" />
                    <x-text-input id="email" name="email" type="email" class="mt-1.5 block w-full" :value="old('email', $user->email)" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                </div>

                <div>
                    <x-input-label for="role" value="Access Role" />
                    <select id="role" name="role" required class="mt-1.5 block w-full rounded-md border-silver bg-ivory/70 text-sm text-navy focus:border-navy focus:bg-paper focus:ring-navy">
                        <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin (Head Curator)</option>
                        <option value="staff" @selected(old('role', $user->role) === 'staff')>Staff (Desk Librarian)</option>
                        <option value="member" @selected(old('role', $user->role) === 'member')>Member (Reader)</option>
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-1.5" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-2 border-t border-silver">
                <div>
                    <x-input-label for="password" value="New Password (Optional)" />
                    <x-text-input id="password" name="password" type="password" class="mt-1.5 block w-full" placeholder="Leave blank to keep current" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" value="Confirm New Password" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1.5 block w-full" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
                </div>
            </div>

            <div class="pt-4 border-t border-silver flex items-center justify-end gap-3">
                <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-ivory border border-silver rounded-md text-sm font-medium text-navy hover:bg-sand/20 transition">
                    Cancel
                </a>
                <x-primary-button>
                    Update User
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
