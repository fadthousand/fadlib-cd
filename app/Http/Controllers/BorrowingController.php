<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;

class BorrowingController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->trim()->toString();
        $search = $request->string('search')->trim()->toString();

        $borrowings = Borrowing::query()
            ->with(['member', 'operator', 'details.book'])
            ->when(in_array($status, ['borrowed', 'returned'], true), fn ($query) => $query->where('status', $status))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery->where('transaction_code', 'like', "%{$search}%")
                        ->orWhereHas('member', function ($memberQuery) use ($search): void {
                            $memberQuery->where('full_name', 'like', "%{$search}%")
                                ->orWhere('member_code', 'like', "%{$search}%");
                        });
                });
            })
            ->latest('borrow_date')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('borrowings.index', compact('borrowings', 'status', 'search'));
    }

    public function create(): View
    {
        $members = Member::query()
            ->where('status', 'active')
            ->orderBy('full_name')
            ->get();

        $books = Book::query()
            ->with('category')
            ->where('stock', '>', 0)
            ->orderBy('volume_code')
            ->get();

        return view('borrowings.create', compact('members', 'books'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'borrow_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.book_id' => ['required', 'distinct', 'exists:books,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        $operator = $request->user();
        if ($operator === null) {
            abort(403);
        }

        try {
            $borrowing = DB::transaction(function () use ($validated, $operator): Borrowing {
                /** @var Member $member */
                $member = Member::query()->findOrFail($validated['member_id']);
                if ($member->status !== 'active') {
                    throw new RuntimeException('Selected member account is currently suspended.');
                }

                $items = collect($validated['items']);
                $bookIds = $items->pluck('book_id')->all();

                $books = Book::query()
                    ->whereIn('id', $bookIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                foreach ($items as $item) {
                    /** @var Book|null $book */
                    $book = $books->get((int) $item['book_id']);
                    $requestedQty = (int) $item['quantity'];

                    if ($book === null || $book->stock < $requestedQty) {
                        $label = $book !== null ? "{$book->volume_code} ({$book->title})" : 'Selected book';
                        throw new RuntimeException("Insufficient available stock for {$label}.");
                    }
                }

                $borrowDate = Carbon::parse($validated['borrow_date']);
                $dueDate = $borrowDate->copy()->addDays(7);

                /** @var Borrowing $createdBorrowing */
                $createdBorrowing = Borrowing::query()->create([
                    'transaction_code' => 'BRW-'.now()->format('Ymd').'-'.strtoupper(Str::random(4)),
                    'user_id' => $operator->id,
                    'member_id' => $member->id,
                    'borrow_date' => $borrowDate->toDateString(),
                    'due_date' => $dueDate->toDateString(),
                    'status' => 'borrowed',
                    'notes' => $validated['notes'] ?? null,
                ]);

                foreach ($items as $item) {
                    /** @var Book $book */
                    $book = $books->get((int) $item['book_id']);
                    $requestedQty = (int) $item['quantity'];

                    BorrowingDetail::query()->create([
                        'borrowing_id' => $createdBorrowing->id,
                        'book_id' => $book->id,
                        'quantity' => $requestedQty,
                    ]);

                    $book->decrement('stock', $requestedQty);
                }

                return $createdBorrowing;
            });
        } catch (RuntimeException $exception) {
            return back()
                ->withInput()
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('borrowings.show', $borrowing)
            ->with('status', 'Borrowing transaction recorded with a 7-day due date.');
    }

    public function show(Borrowing $borrowing): View
    {
        $borrowing->load(['member', 'operator', 'details.book.category']);

        return view('borrowings.show', compact('borrowing'));
    }

    public function markReturned(Borrowing $borrowing): RedirectResponse
    {
        try {
            DB::transaction(function () use ($borrowing): void {
                /** @var Borrowing $lockedBorrowing */
                $lockedBorrowing = Borrowing::query()
                    ->with('details')
                    ->lockForUpdate()
                    ->findOrFail($borrowing->id);

                if ($lockedBorrowing->status === 'returned') {
                    throw new RuntimeException('This transaction has already been marked as returned.');
                }

                $bookIds = $lockedBorrowing->details->pluck('book_id')->all();
                $books = Book::query()
                    ->whereIn('id', $bookIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                foreach ($lockedBorrowing->details as $detail) {
                    /** @var Book|null $book */
                    $book = $books->get($detail->book_id);
                    if ($book !== null) {
                        $book->increment('stock', $detail->quantity);
                    }
                }

                $lockedBorrowing->update([
                    'return_date' => now()->toDateString(),
                    'status' => 'returned',
                ]);
            });
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('borrowings.show', $borrowing)
            ->with('status', 'Books returned and archive stock restored.');
    }

    public function myShelf(Request $request): View
    {
        $user = $request->user();
        $member = $user?->member;

        $activeBorrowings = collect();
        $returnedBorrowings = collect();

        if ($member !== null) {
            $activeBorrowings = $member->borrowings()
                ->with('details.book.category')
                ->where('status', 'borrowed')
                ->latest('borrow_date')
                ->get();

            $returnedBorrowings = $member->borrowings()
                ->with('details.book.category')
                ->where('status', 'returned')
                ->latest('return_date')
                ->get();
        }

        return view('shelf.index', compact('member', 'activeBorrowings', 'returnedBorrowings'));
    }

    public function memberCheckout(Request $request, Book $book): RedirectResponse
    {
        $user = $request->user();
        $member = $user?->member;

        if ($member === null) {
            return back()->with('error', 'No member profile is linked to your account.');
        }

        if ($member->status !== 'active') {
            return back()->with('error', 'Your membership is currently suspended.');
        }

        try {
            DB::transaction(function () use ($member, $book): void {
                /** @var Book $lockedBook */
                $lockedBook = Book::query()->lockForUpdate()->findOrFail($book->id);

                if ($lockedBook->stock < 1) {
                    throw new RuntimeException('No copies are currently available for borrowing.');
                }

                $alreadyBorrowed = Borrowing::query()
                    ->where('member_id', $member->id)
                    ->where('status', 'borrowed')
                    ->whereHas('details', fn ($query) => $query->where('book_id', $lockedBook->id))
                    ->exists();

                if ($alreadyBorrowed) {
                    throw new RuntimeException('You already have an active loan for this volume.');
                }

                $borrowDate = now();

                /** @var Borrowing $borrowing */
                $borrowing = Borrowing::query()->create([
                    'transaction_code' => 'SLF-'.now()->format('Ymd').'-'.strtoupper(Str::random(4)),
                    'user_id' => null,
                    'member_id' => $member->id,
                    'borrow_date' => $borrowDate->toDateString(),
                    'due_date' => $borrowDate->copy()->addDays(7)->toDateString(),
                    'status' => 'borrowed',
                    'notes' => 'Self-service reader checkout.',
                ]);

                BorrowingDetail::query()->create([
                    'borrowing_id' => $borrowing->id,
                    'book_id' => $lockedBook->id,
                    'quantity' => 1,
                ]);

                $lockedBook->decrement('stock', 1);
            });
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('shelf.index')
            ->with('status', 'Volume borrowed for 7 days. You may now download the EPUB file.');
    }

    public function memberReturn(Request $request, Borrowing $borrowing): RedirectResponse
    {
        $member = $request->user()?->member;

        if ($member === null || $borrowing->member_id !== $member->id) {
            abort(403);
        }

        return $this->markReturned($borrowing)->with('status', 'Volume returned to the archive.');
    }
}
