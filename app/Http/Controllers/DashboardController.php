<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\Member;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalTitles = Book::query()->count();
        $totalStock = (int) Book::query()->sum('stock');
        $totalMembers = Member::query()->count();
        $borrowedCopies = (int) BorrowingDetail::query()
            ->whereHas('borrowing', fn ($query) => $query->where('status', 'borrowed'))
            ->sum('quantity');
        $activeLoansCount = Borrowing::query()->where('status', 'borrowed')->count();

        $recentBorrowings = Borrowing::query()
            ->with(['member', 'operator', 'details.book'])
            ->latest()
            ->take(6)
            ->get();

        return view('dashboard', compact(
            'totalTitles',
            'totalStock',
            'totalMembers',
            'borrowedCopies',
            'activeLoansCount',
            'recentBorrowings'
        ));
    }
}
