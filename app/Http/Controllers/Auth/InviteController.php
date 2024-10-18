<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Models\Invite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class InviteController extends Controller
{
    public function show($id)
    {
        // pega o invite
        $invite = Invite::where('id', $id)->first();
        if (!$invite) {
            abort(404);
        }

        return Inertia::render('Auth/Invitation', [
            'id' => $invite->id,
            'email' => $invite->email,
        ]);
    }

    public function accept(Request $request, $id)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'name' => ['required', 'max:255'],
            'password' => ['required', 'max:255', 'confirmed'],
        ]);

        // valida o id
        $invite = Invite::where('id', $id)
            ->where('email', $request->email)
            ->with('workspace')
            ->first();

        // se não encontrar o token
        if (!$invite) {
            session()->flash('flash.banner', 'Invalid Invite.');
            session()->flash('flash.bannerStyle', 'danger');

            return redirect(route('auth.invites.show', $invite->id));
        }

        // user
        $user = new User();
        $user->name = $request->name;
        $user->email = $invite->email;
        $user->password = Hash::make($request->password);
        $user->current_workspace_id = $invite->workspace_id;
        $user->email_verified_at = now();
        $user->save();

        // attach user to store
        $user->workspaces()->attach($invite->workspace_id, [
            'role' => $invite->role,
        ]);

        Auth::login($user);

        // deleta o invite
        $invite->delete();

        return Inertia::location(route('links.index'));
    }
}
