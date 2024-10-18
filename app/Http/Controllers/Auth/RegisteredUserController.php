<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

use App\Enums\User\Role;

use App\Models\Plan;
use App\Models\User;
use App\Models\Workspace;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
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

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $workspace = Workspace::create([
            'name' => $request->name,
            'plan_id' => Plan::where('internal_id', 'free')->first()->id,
        ]);

        // attach user to project
        $user->workspaces()->attach($workspace->id, [
            'role' => Role::ROLE_OWNER,
        ]);

        $user->forceFill([
            'current_workspace_id' => $workspace->id,
        ])->save();

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('links.index', absolute: false));
    }
}
