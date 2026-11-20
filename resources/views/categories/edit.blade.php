<x-app-layout>
    <x-slot name="title">Edit Series</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="font-mono text-xs uppercase tracking-widest text-bronze">Categories</p>
                <h1 class="font-serif text-3xl font-medium text-navy mt-1">Edit Series</h1>
            </div>
            <a href="{{ route('categories.index') }}" class="text-sm text-slate hover:text-navy underline">
                &larr; Back to Series
            </a>
        </div>
    </x-slot>

    <div class="max-w-2xl bg-paper border border-silver rounded-lg p-6 sm:p-8">
        <form method="POST" action="{{ route('categories.update', $category) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <x-input-label for="name" value="Series Title" />
                <x-text-input id="name" name="name" type="text" class="mt-1.5 block w-full" :value="old('name', $category->name)" required />
                <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="author" value="Author" />
                <x-text-input id="author" name="author" type="text" class="mt-1.5 block w-full" :value="old('author', $category->author)" required />
                <x-input-error :messages="$errors->get('author')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="epigraph" value="Epigraph (Optional)" />
                <x-text-input id="epigraph" name="epigraph" type="text" class="mt-1.5 block w-full font-serif italic" :value="old('epigraph', $category->epigraph)" />
                <x-input-error :messages="$errors->get('epigraph')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="description" value="Series Description (Optional)" />
                <textarea id="description" name="description" rows="3" class="mt-1.5 block w-full rounded-md border-silver bg-ivory/70 text-sm text-navy focus:border-navy focus:bg-paper focus:ring-navy">{{ old('description', $category->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-1.5" />
            </div>

            <div class="pt-4 border-t border-silver flex items-center justify-end gap-3">
                <a href="{{ route('categories.index') }}" class="inline-flex items-center px-4 py-2 bg-ivory border border-silver rounded-md text-sm font-medium text-navy hover:bg-sand/20 transition">
                    Cancel
                </a>
                <x-primary-button>
                    Update Series
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
