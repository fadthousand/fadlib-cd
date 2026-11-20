<x-app-layout>
    <x-slot name="title">Edit {{ $member->member_code }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="font-mono text-xs uppercase tracking-widest text-bronze">Member Management</p>
                <h1 class="font-serif text-3xl font-medium text-navy mt-1">Edit Member</h1>
            </div>
            <a href="{{ route('members.show', $member) }}" class="text-sm text-slate hover:text-navy underline">
                &larr; Back to Details
            </a>
        </div>
    </x-slot>

    <div class="max-w-2xl bg-paper border border-silver rounded-lg p-6 sm:p-8">
        <form method="POST" action="{{ route('members.update', $member) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <x-input-label for="member_code" value="Member Code" />
                    <x-text-input id="member_code" name="member_code" type="text" class="mt-1.5 block w-full font-mono uppercase" :value="old('member_code', $member->member_code)" required />
                    <x-input-error :messages="$errors->get('member_code')" class="mt-1.5" />
                </div>

                <div>
                    <x-input-label for="status" value="Membership Status" />
                    <select id="status" name="status" required class="mt-1.5 block w-full rounded-md border-silver bg-ivory/70 text-sm text-navy focus:border-navy focus:bg-paper focus:ring-navy">
                        <option value="active" @selected(old('status', $member->status) === 'active')>Active</option>
                        <option value="suspended" @selected(old('status', $member->status) === 'suspended')>Suspended</option>
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-1.5" />
                </div>
            </div>

            <div>
                <x-input-label for="full_name" value="Full Name" />
                <x-text-input id="full_name" name="full_name" type="text" class="mt-1.5 block w-full" :value="old('full_name', $member->full_name)" required />
                <x-input-error :messages="$errors->get('full_name')" class="mt-1.5" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <x-input-label for="email" value="Email Address" />
                    <x-text-input id="email" name="email" type="email" class="mt-1.5 block w-full" :value="old('email', $member->email)" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                </div>

                <div>
                    <x-input-label for="phone" value="Phone Number (Optional)" />
                    <x-text-input id="phone" name="phone" type="text" class="mt-1.5 block w-full font-mono" :value="old('phone', $member->phone)" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-1.5" />
                </div>
            </div>

            <div>
                <x-input-label for="address" value="Address (Optional)" />
                <textarea id="address" name="address" rows="3" class="mt-1.5 block w-full rounded-md border-silver bg-ivory/70 text-sm text-navy focus:border-navy focus:bg-paper focus:ring-navy">{{ old('address', $member->address) }}</textarea>
                <x-input-error :messages="$errors->get('address')" class="mt-1.5" />
            </div>

            <div class="pt-4 border-t border-silver flex items-center justify-end gap-3">
                <a href="{{ route('members.show', $member) }}" class="inline-flex items-center px-4 py-2 bg-ivory border border-silver rounded-md text-sm font-medium text-navy hover:bg-sand/20 transition">
                    Cancel
                </a>
                <x-primary-button>
                    Update Member
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
