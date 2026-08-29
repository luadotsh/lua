<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\User\CreateUser;
use App\Http\Controllers\Auth\Concerns\PreservesAttributionParameters;
use App\Http\Controllers\Controller;

use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

use App\Models\User;

class RegisteredUserController extends Controller
{
    use PreservesAttributionParameters;

    /**
     * Display the registration view.
     */
    public function create(Request $request): Response
    {
        $this->storeAttributionParameters($request);

        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', Rules\Password::defaults()],
        ]);

        $user = CreateUser::execute([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ], $this->retrieveAttributionParameters());

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('links.index', absolute: false));
    }
}
