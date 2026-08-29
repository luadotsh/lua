<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\AcceptInviteRequest;
use App\Actions\User\CreateUser;
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

    public function accept(AcceptInviteRequest $request, $id)
    {
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

        Auth::login($user);

        // deleta o invite
        $invite->delete();

        return Inertia::location(route('links.index'));
    }
}
