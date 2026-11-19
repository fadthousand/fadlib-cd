<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();

        $members = Member::query()
            ->with('user')
            ->withCount(['borrowings as active_loans_count' => fn ($query) => $query->where('status', 'borrowed')])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery->where('member_code', 'like', "%{$search}%")
                        ->orWhere('full_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('member_code')
            ->paginate(12)
            ->withQueryString();

        return view('members.index', compact('members', 'search'));
    }

    public function show(Member $member): View
    {
        $member->load('user');

        $borrowings = $member->borrowings()
            ->with(['operator', 'details.book.category'])
            ->latest('borrow_date')
            ->latest('id')
            ->get();

        return view('members.show', compact('member', 'borrowings'));
    }

    public function create(): View
    {
        return view('members.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'member_code' => ['required', 'string', 'max:30', 'unique:members,member_code'],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:members,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['active', 'suspended'])],
        ]);

        Member::query()->create([
            'member_code' => strtoupper(trim($validated['member_code'])),
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('members.index')
            ->with('status', 'Member registered successfully.');
    }

    public function edit(Member $member): View
    {
        return view('members.edit', compact('member'));
    }

    public function update(Request $request, Member $member): RedirectResponse
    {
        $validated = $request->validate([
            'member_code' => ['required', 'string', 'max:30', Rule::unique('members', 'member_code')->ignore($member->id)],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('members', 'email')->ignore($member->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['active', 'suspended'])],
        ]);

        $member->update([
            'member_code' => strtoupper(trim($validated['member_code'])),
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('members.show', $member)
            ->with('status', 'Member profile updated.');
    }

    public function destroy(Member $member): RedirectResponse
    {
        if ($member->borrowings()->exists()) {
            return back()->with('error', 'Cannot delete a member who has existing borrowing history.');
        }

        $member->delete();

        return redirect()
            ->route('members.index')
            ->with('status', 'Member record deleted.');
    }
}
