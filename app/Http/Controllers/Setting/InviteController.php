<?php

declare(strict_types=1);

namespace App\Http\Controllers\Setting;

use App\Actions\Invite\GetInvite;
use App\Actions\Invite\DeleteInvite;
use App\Actions\Invite\CreateInvite;
use App\Http\Requests\Invite\InviteRequest;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Gate;

use App\Models\User;
use App\Models\Invite;

use App\Mail\Team\SendUserInvite;

use Inertia\Inertia;

class InviteController extends Controller
{
    public function create()
    {
        return Inertia::render('Setting/TeamMember/Invite/Create');
    }

    public function store(InviteRequest $request)
    {
        $workspace = auth()->user()->currentWorkspace;

        $response = Gate::inspect('reached-user-limit', $workspace);
        if (!$response->allowed()) {
            session()->flash('flash.banner', 'You have reached the limit of team members, please upgrade your plan.');
            session()->flash('flash.bannerStyle', 'danger');
            return redirect()->route('setting.team-members.index');
        }

        // check if email already exist
        $user = User::where('email', $request->email)->first();

        $invite = CreateInvite::execute($workspace, $request->validated());

        if ($invite === null) {
            session()->flash('flash.banner', 'User was added to team!');
            session()->flash('flash.bannerStyle', 'success');

            return back();
        }

        session()->flash('flash.banner', 'Invite was sent!');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }

    public function destroy($id)
    {
        $workspace = auth()->user()->currentWorkspace;

        $invite = GetInvite::execute($workspace, $id);

        abort_unless($invite, 404);

        DeleteInvite::execute($invite);

        session()->flash('flash.banner', 'Invite deleted successful.');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }
}
