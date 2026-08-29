<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\LoginUser;
use App\Http\Requests\Auth\RegisterRequest;
use App\Actions\User\CreateUser;
use App\Http\Controllers\Auth\Concerns\PreservesAttributionParameters;
use App\Http\Controllers\Controller;

use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = CreateUser::execute([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ], $this->retrieveAttributionParameters());

        event(new Registered($user));

        LoginUser::forUser($request, $user);

        return redirect(route('links.index', absolute: false));
    }
}
