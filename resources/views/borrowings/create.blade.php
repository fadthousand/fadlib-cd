@php
    $initialItems = old('items', [
        ['book_id' => '', 'quantity' => 1],
    ]);

    $bookCatalog = $books->map(fn ($book) => [
        'id' => $book->id,
        'label' => "[{$book->volume_code}] " . ($book->category?->name ?? 'Series') . " — {$book->title} ({$book->stock} available)",
        'stock' => $book->stock,
    ])->values();
@endphp

<x-app-layout>
    <x-slot name="title">Record Borrowing</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="font-mono text-xs uppercase tracking-widest text-bronze">Circulation</p>
                <h1 class="font-serif text-3xl font-medium text-navy mt-1">Record Borrowing</h1>
            </div>
            <a href="{{ route('borrowings.index') }}" class="text-sm text-slate hover:text-navy underline">
                &larr; Back to Borrowings
            </a>
        </div>
    </x-slot>

    <div
        x-data="{
            items: @js(array_values($initialItems)),
            books: @js($bookCatalog),
            borrowDate: @js(old('borrow_date', now()->toDateString())),
            addRow() {
                this.items.push({ book_id: '', quantity: 1 });
            },
            removeRow(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                }
            },
            maxStockFor(bookId) {
                const found = this.books.find(b => String(b.id) === String(bookId));
                return found ? found.stock : 10;
            },
            computedDueDate() {
                if (!this.borrowDate) return '—';
                const parts = this.borrowDate.split('-').map(Number);
                if (parts.length !== 3) return '—';
                const dt = new Date(Date.UTC(parts[0], parts[1] - 1, parts[2]));
                dt.setUTCDate(dt.getUTCDate() + 7);
                return dt.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric', timeZone: 'UTC' });
            }
        }"
        class="max-w-4xl bg-paper border border-silver rounded-lg p-6 sm:p-8"
    >
        <form method="POST" action="{{ route('borrowings.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div class="sm:col-span-2">
                    <x-input-label for="member_id" value="Member" />
                    <select id="member_id" name="member_id" required class="mt-1.5 block w-full rounded-md border-silver bg-ivory/70 text-sm text-navy focus:border-navy focus:bg-paper focus:ring-navy">
                        <option value="">Select an active member...</option>
                        @foreach ($members as $member)
                            <option value="{{ $member->id }}" @selected((int) old('member_id') === $member->id)>
                                [{{ $member->member_code }}] {{ $member->full_name }} ({{ $member->email }})
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('member_id')" class="mt-1.5" />
                </div>

                <div>
                    <x-input-label for="borrow_date" value="Borrow Date" />
                    <input
                        id="borrow_date"
                        name="borrow_date"
                        type="date"
                        x-model="borrowDate"
                        required
                        class="mt-1.5 block w-full rounded-md border-silver bg-ivory/70 text-sm font-mono text-navy focus:border-navy focus:bg-paper focus:ring-navy"
                    />
                    <p class="font-mono text-[11px] text-bronze mt-1">
                        Due (+7 days): <span x-text="computedDueDate()"></span>
                    </p>
                    <x-input-error :messages="$errors->get('borrow_date')" class="mt-1.5" />
                </div>
            </div>

            <div class="pt-4 border-t border-silver">
                <div class="flex items-center justify-between gap-4 mb-3">
                    <div>
                        <p class="font-mono text-xs uppercase tracking-wider text-slate">Books to Borrow</p>
                        <p class="text-xs text-slate">Each book can only be selected once per transaction.</p>
                    </div>

                    <button type="button" @click="addRow()" class="inline-flex items-center px-3 py-1.5 bg-ivory border border-silver rounded text-xs font-medium text-navy hover:bg-sand/20 transition">
                        + Add Book
                    </button>
                </div>

                <x-input-error :messages="$errors->get('items')" class="mb-2" />

                <div class="space-y-3">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-start bg-ivory/60 border border-silver rounded-md p-3">
                            <div class="sm:col-span-8">
                                <label :for="'book_' + index" class="block font-mono text-[10px] uppercase tracking-wider text-slate mb-1">
                                    Select Book
                                </label>
                                <select
                                    :id="'book_' + index"
                                    :name="'items[' + index + '][book_id]'"
                                    x-model="item.book_id"
                                    required
                                    class="block w-full rounded-md border-silver bg-paper text-sm text-navy focus:border-navy focus:ring-navy"
                                >
                                    <option value="">Select an available book...</option>
                                    <template x-for="book in books" :key="book.id">
                                        <option :value="book.id" x-text="book.label" :selected="String(book.id) === String(item.book_id)"></option>
                                    </template>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label :for="'qty_' + index" class="block font-mono text-[10px] uppercase tracking-wider text-slate mb-1">
                                    Copies
                                </label>
                                <input
                                    :id="'qty_' + index"
                                    :name="'items[' + index + '][quantity]'"
                                    type="number"
                                    min="1"
                                    :max="maxStockFor(item.book_id)"
                                    x-model.number="item.quantity"
                                    required
                                    class="block w-full rounded-md border-silver bg-paper text-sm font-mono text-navy focus:border-navy focus:ring-navy"
                                />
                            </div>

                            <div class="sm:col-span-2 sm:pt-5 flex justify-end">
                                <button
                                    type="button"
                                    @click="removeRow(index)"
                                    :disabled="items.length === 1"
                                    :class="items.length === 1 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-terracotta hover:text-paper'"
                                    class="w-full sm:w-auto inline-flex justify-center items-center px-3 py-2 bg-terracotta/10 border border-terracotta/40 text-terracotta text-xs font-medium rounded-md transition"
                                >
                                    Remove
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                @if ($errors->has('items.*'))
                    <ul class="text-xs text-terracotta space-y-1 mt-2">
                        @foreach ($errors->get('items.*') as $messages)
                            @foreach ($messages as $message)
                                <li>{{ $message }}</li>
                            @endforeach
                        @endforeach
                    </ul>
                @endif
            </div>

            <div>
                <x-input-label for="notes" value="Notes (Optional)" />
                <textarea id="notes" name="notes" rows="2" class="mt-1.5 block w-full rounded-md border-silver bg-ivory/70 text-sm text-navy focus:border-navy focus:bg-paper focus:ring-navy" placeholder="Optional notes...">{{ old('notes') }}</textarea>
                <x-input-error :messages="$errors->get('notes')" class="mt-1.5" />
            </div>

            <div class="pt-4 border-t border-silver flex items-center justify-end gap-3">
                <a href="{{ route('borrowings.index') }}" class="inline-flex items-center px-4 py-2 bg-ivory border border-silver rounded-md text-sm font-medium text-navy hover:bg-sand/20 transition">
                    Cancel
                </a>
                <x-primary-button>
                    Save Borrowing
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
