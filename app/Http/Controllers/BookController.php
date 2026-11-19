<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();
        $selectedCategoryId = $request->integer('category');

        $allCategories = Category::query()->orderBy('name')->get();

        $seriesList = Category::query()
            ->when($selectedCategoryId > 0, fn ($query) => $query->where('id', $selectedCategoryId))
            ->whereHas('books', function ($query) use ($search): void {
                if ($search !== '') {
                    $query->where(function ($subQuery) use ($search): void {
                        $subQuery->where('title', 'like', "%{$search}%")
                            ->orWhere('volume_code', 'like', "%{$search}%")
                            ->orWhere('author', 'like', "%{$search}%");
                    });
                }
            })
            ->with(['books' => function ($query) use ($search): void {
                if ($search !== '') {
                    $query->where(function ($subQuery) use ($search): void {
                        $subQuery->where('title', 'like', "%{$search}%")
                            ->orWhere('volume_code', 'like', "%{$search}%")
                            ->orWhere('author', 'like', "%{$search}%");
                    });
                }
                $query->orderBy('volume_code');
            }])
            ->orderBy('name')
            ->get();

        $totalVolumes = $seriesList->sum(fn (Category $category) => $category->books->count());

        return view('books.index', compact(
            'seriesList',
            'allCategories',
            'search',
            'selectedCategoryId',
            'totalVolumes'
        ));
    }

    public function show(Request $request, Book $book): View
    {
        $book->load('category');

        $activeMemberBorrowing = null;
        $user = $request->user();

        if ($user !== null && $user->isMember() && $user->member !== null) {
            $activeMemberBorrowing = Borrowing::query()
                ->where('member_id', $user->member->id)
                ->where('status', 'borrowed')
                ->whereHas('details', fn ($query) => $query->where('book_id', $book->id))
                ->first();
        }

        return view('books.show', compact('book', 'activeMemberBorrowing'));
    }

    public function create(): View
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('books.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'volume_code' => ['required', 'string', 'max:30', 'unique:books,volume_code'],
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'publication_year' => ['nullable', 'integer', 'min:1950', 'max:'.(now()->year + 1)],
            'synopsis' => ['nullable', 'string'],
            'curator_note' => ['nullable', 'string'],
            'total_copies' => ['required', 'integer', 'min:1', 'max:999'],
            'epub_file' => ['nullable', 'file', 'max:102400'],
        ]);

        $epubPath = null;
        if ($request->hasFile('epub_file')) {
            /** @var UploadedFile $file */
            $file = $request->file('epub_file');
            $epubPath = $this->storeNormalizedEpub($file, $validated['volume_code']);
        }

        Book::query()->create([
            'category_id' => $validated['category_id'],
            'volume_code' => strtoupper(trim($validated['volume_code'])),
            'title' => $validated['title'],
            'author' => $validated['author'],
            'publication_year' => $validated['publication_year'] ?? null,
            'synopsis' => $validated['synopsis'] ?? null,
            'curator_note' => $validated['curator_note'] ?? null,
            'epub_path' => $epubPath,
            'stock' => (int) $validated['total_copies'],
            'total_copies' => (int) $validated['total_copies'],
        ]);

        return redirect()
            ->route('books.index')
            ->with('status', 'Volume added to the archive.');
    }

    public function edit(Book $book): View
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('books.edit', compact('book', 'categories'));
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'volume_code' => ['required', 'string', 'max:30', Rule::unique('books', 'volume_code')->ignore($book->id)],
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'publication_year' => ['nullable', 'integer', 'min:1950', 'max:'.(now()->year + 1)],
            'synopsis' => ['nullable', 'string'],
            'curator_note' => ['nullable', 'string'],
            'stock' => ['required', 'integer', 'min:0', 'lte:total_copies'],
            'total_copies' => ['required', 'integer', 'min:1', 'max:999'],
            'epub_file' => ['nullable', 'file', 'max:102400'],
        ]);

        $epubPath = $book->epub_path;
        if ($request->hasFile('epub_file')) {
            if ($epubPath !== null && Storage::disk(config('filesystems.epub_disk'))->exists($epubPath)) {
                Storage::disk(config('filesystems.epub_disk'))->delete($epubPath);
            }

            /** @var UploadedFile $file */
            $file = $request->file('epub_file');
            $epubPath = $this->storeNormalizedEpub($file, $validated['volume_code']);
        }

        $book->update([
            'category_id' => $validated['category_id'],
            'volume_code' => strtoupper(trim($validated['volume_code'])),
            'title' => $validated['title'],
            'author' => $validated['author'],
            'publication_year' => $validated['publication_year'] ?? null,
            'synopsis' => $validated['synopsis'] ?? null,
            'curator_note' => $validated['curator_note'] ?? null,
            'epub_path' => $epubPath,
            'stock' => (int) $validated['stock'],
            'total_copies' => (int) $validated['total_copies'],
        ]);

        return redirect()
            ->route('books.show', $book)
            ->with('status', 'Volume details updated.');
    }

    public function destroy(Book $book): RedirectResponse
    {
        if ($book->borrowingDetails()->exists()) {
            return back()->with('error', 'Cannot delete a volume that is referenced in circulation records.');
        }

        if ($book->epub_path !== null && Storage::disk(config('filesystems.epub_disk'))->exists($book->epub_path)) {
            Storage::disk(config('filesystems.epub_disk'))->delete($book->epub_path);
        }

        $book->delete();

        return redirect()
            ->route('books.index')
            ->with('status', 'Volume removed from the archive.');
    }

    private function storeNormalizedEpub(UploadedFile $file, string $volumeCode): string
    {
        $slugCode = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', trim($volumeCode)) ?? 'volume');
        $filename = trim($slugCode, '-').'.epub';

        Storage::disk(config('filesystems.epub_disk'))->putFileAs('epubs', $file, $filename);

        return "epubs/{$filename}";
    }
}
