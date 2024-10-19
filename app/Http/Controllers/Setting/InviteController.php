<?php

namespace App\Http\Controllers\Setting;

use App\Http\Requests\Invite\InviteRequest;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\Invite;

use App\Mail\SendUserInvitation;

class InviteController extends Controller
{
    public function store(InviteRequest $request)
    {
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
        Mail::to($invite->email)->send(new SendUserInvitation($workspace, $invite));

        // update feature flags
        $workspace->updateSetupGuide('team');

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
