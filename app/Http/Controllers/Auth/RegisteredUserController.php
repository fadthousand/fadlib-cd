<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        /** @var User $user */
        $user = DB::transaction(function () use ($request): User {
            /** @var User $createdUser */
            $createdUser = User::query()->create([
                'name' => $request->string('name')->toString(),
                'email' => $request->string('email')->toString(),
                'password' => Hash::make($request->string('password')->toString()),
                'role' => 'member',
            ]);

            Member::query()->create([
                'user_id' => $createdUser->id,
                'member_code' => 'MEM-'.now()->year.'-U'.$createdUser->id,
                'full_name' => $createdUser->name,
                'email' => $createdUser->email,
                'status' => 'active',
            ]);

            return $createdUser;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('books.index', absolute: false));
    }
}
