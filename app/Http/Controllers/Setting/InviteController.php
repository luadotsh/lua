<?php

declare(strict_types=1);

namespace App\Http\Controllers\Setting;

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
        $response = Gate::inspect('reached-user-limit', $request->workspace);
        if (!$response->allowed()) {
            session()->flash('flash.banner', 'You have reached the limit of team members, please upgrade your plan.');
            session()->flash('flash.bannerStyle', 'danger');
            return redirect()->route('setting.team-members.index');
        }

        $workspace = auth()->user()->currentWorkspace;

        // check if email already exist
        $user = User::where('email', $request->email)->first();

        // if user already exists
        if ($user) {

            // attach user to project
            $user->workspaces()->syncWithPivotValues([
                $workspace->id
            ], ['role' => $request->role], false);

            session()->flash('flash.banner', 'User was added to team!');
            session()->flash('flash.bannerStyle', 'success');

            return back();
        }

        // create invite
        $invite = new Invite;
        $invite->workspace_id = $workspace->id;
        $invite->email = $request->email;
        $invite->role = $request->role;
        $invite->save();

        // send email
        Mail::to($invite->email)->send(new SendUserInvite($workspace, $invite));

        session()->flash('flash.banner', 'Invite was sent!');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }

    public function destroy($id)
    {
        $workspace = auth()->user()->currentWorkspace;

        $invite = Invite::where('id', $id)->where('workspace_id', $workspace->id)->firstOrFail();
        $invite->delete();

        session()->flash('flash.banner', 'Invite deleted successful.');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }
}
