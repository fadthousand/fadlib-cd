<x-app-layout>
    <x-slot name="title">Edit {{ $book->volume_code }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="font-mono text-xs uppercase tracking-widest text-bronze">Book Management</p>
                <h1 class="font-serif text-3xl font-medium text-navy mt-1">Edit {{ $book->volume_code }}</h1>
            </div>
            <a href="{{ route('books.show', $book) }}" class="text-sm text-slate hover:text-navy underline">
                &larr; Back to Details
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl bg-paper border border-silver rounded-lg p-6 sm:p-8">
        <form method="POST" action="{{ route('books.update', $book) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <x-input-label for="category_id" value="Series" />
                    <select id="category_id" name="category_id" required class="mt-1.5 block w-full rounded-md border-silver bg-ivory/70 text-sm text-navy focus:border-navy focus:bg-paper focus:ring-navy">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((int) old('category_id', $book->category_id) === $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('category_id')" class="mt-1.5" />
                </div>

                <div>
                    <x-input-label for="volume_code" value="Volume Code" />
                    <x-text-input id="volume_code" name="volume_code" type="text" class="mt-1.5 block w-full font-mono uppercase" :value="old('volume_code', $book->volume_code)" required />
                    <x-input-error :messages="$errors->get('volume_code')" class="mt-1.5" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <x-input-label for="title" value="Volume Title" />
                    <x-text-input id="title" name="title" type="text" class="mt-1.5 block w-full" :value="old('title', $book->title)" required />
                    <x-input-error :messages="$errors->get('title')" class="mt-1.5" />
                </div>

                <div>
                    <x-input-label for="author" value="Author" />
                    <x-text-input id="author" name="author" type="text" class="mt-1.5 block w-full" :value="old('author', $book->author)" required />
                    <x-input-error :messages="$errors->get('author')" class="mt-1.5" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <x-input-label for="publication_year" value="Publication Year" />
                    <x-text-input id="publication_year" name="publication_year" type="number" min="1950" max="{{ now()->year + 1 }}" class="mt-1.5 block w-full font-mono" :value="old('publication_year', $book->publication_year)" />
                    <x-input-error :messages="$errors->get('publication_year')" class="mt-1.5" />
                </div>

                <div>
                    <x-input-label for="stock" value="Available Stock" />
                    <x-text-input id="stock" name="stock" type="number" min="0" max="999" class="mt-1.5 block w-full font-mono" :value="old('stock', $book->stock)" required />
                    <x-input-error :messages="$errors->get('stock')" class="mt-1.5" />
                </div>

                <div>
                    <x-input-label for="total_copies" value="Total Copies" />
                    <x-text-input id="total_copies" name="total_copies" type="number" min="1" max="999" class="mt-1.5 block w-full font-mono" :value="old('total_copies', $book->total_copies)" required />
                    <x-input-error :messages="$errors->get('total_copies')" class="mt-1.5" />
                </div>
            </div>

            <div>
                <x-input-label for="epub_file" value="Replace EPUB File (Optional)" />
                <input id="epub_file" name="epub_file" type="file" accept=".epub,application/epub+zip" class="mt-1.5 block w-full text-sm text-slate file:mr-4 file:py-2 file:px-4 file:rounded-md file:border file:border-silver file:text-xs file:font-medium file:bg-ivory file:text-navy hover:file:bg-sand/20" />
                <p class="font-mono text-xs text-slate mt-1">
                    Current file: {{ $book->epub_path ?? 'None attached' }}
                </p>
                <x-input-error :messages="$errors->get('epub_file')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="synopsis" value="Synopsis (Optional)" />
                <textarea id="synopsis" name="synopsis" rows="4" class="mt-1.5 block w-full rounded-md border-silver bg-ivory/70 text-sm text-navy focus:border-navy focus:bg-paper focus:ring-navy">{{ old('synopsis', $book->synopsis) }}</textarea>
                <x-input-error :messages="$errors->get('synopsis')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="curator_note" value="Notes (Optional)" />
                <textarea id="curator_note" name="curator_note" rows="2" class="mt-1.5 block w-full rounded-md border-silver bg-ivory/70 text-sm text-navy focus:border-navy focus:bg-paper focus:ring-navy">{{ old('curator_note', $book->curator_note) }}</textarea>
                <x-input-error :messages="$errors->get('curator_note')" class="mt-1.5" />
            </div>

            <div class="pt-4 border-t border-silver flex items-center justify-end gap-3">
                <a href="{{ route('books.show', $book) }}" class="inline-flex items-center px-4 py-2 bg-ivory border border-silver rounded-md text-sm font-medium text-navy hover:bg-sand/20 transition">
                    Cancel
                </a>
                <x-primary-button>
                    Update Book
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
