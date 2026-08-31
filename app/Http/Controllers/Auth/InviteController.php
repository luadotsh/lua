<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\LoginUser;
use App\Actions\User\CreateUser;
use App\Http\Requests\Auth\AcceptInviteRequest;
use App\Models\Invite;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class InviteController extends Controller
{
    public function show($id)
    {
        // pega o invite
        $invite = Invite::where('id', $id)->first();
        if (! $invite) {
            abort(404);
        }

        return Inertia::render('Auth/Invitation', [
            'id' => $invite->id,
            'email' => $invite->email,
        ]);
    }

    public function accept(AcceptInviteRequest $request, $id)
    {
        // valida o id
        $invite = Invite::where('id', $id)
            ->where('email', $request->email)
            ->with('workspace')
            ->first();

        // The redirect used to read $invite->id on the null it had just
        // checked for, so a wrong email — or a second attempt at an invite
        // already consumed — was a fatal rather than this message.
        if (! $invite) {
            throw ValidationException::withMessages([
                'email' => 'This invitation is not for that email address, or it has already been used.',
            ]);
        }

        // Someone who already has an account cannot register again; joining is
        // what they need, and CreateInvite does that up front for anyone who
        // had an account when the invite was written.
        if (User::where('email', $invite->email)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'An account already exists for this address. Sign in and ask to be added again.',
            ]);
        }

        // user — is_invite keeps CreateUser from also making a personal
        // workspace, since this user joins the one that invited them
        $user = CreateUser::execute([
            'name' => $request->name,
            'email' => $invite->email,
            'password' => Hash::make($request->password),
            'current_workspace_id' => $invite->workspace_id,
            'is_invite' => true,
            'invite_role' => $invite->role,
        ]);

        LoginUser::forUser($request, $user);

        // deleta o invite
        $invite->delete();

        return Inertia::location(route('links.index'));
    }
}
