<?php

declare(strict_types=1);

namespace App\Http\Controllers\Setting;

use App\Actions\TeamMember\UpdateMemberRole;
use App\Actions\TeamMember\RemoveMember;
use App\Http\Requests\TeamMember\UpdateUserRoleRequest;

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Models\Workspace;
use App\Models\User;
use App\Models\Invite;

class TeamMemberController extends Controller
{
    public function index(Request $request)
    {
        $workspace = auth()->user()->currentWorkspace;

        $workspace = Workspace::where('id', $workspace->id)
            ->with('users', function ($query) use ($request) {
                $query->orderBy('name', 'asc');
                if($request->q) {
                    $query->where('name', 'like', '%'.$request->q.'%');
                    $query->orWhere('email', 'like', '%'.$request->q.'%');
                }
            })->first();

        $invites = Invite::where('workspace_id', $workspace->id)
        ->get();

        return Inertia::render('Setting/TeamMember/Index', [
            'users' => $workspace->users,
            'invites' => $invites
        ]);
    }

    public function updateUserRole($id, UpdateUserRoleRequest $request)
    {
        // validate user
        $user = User::where('id', $id)
        ->whereHas('workspaces', function (Builder $query) {
            $query->where('workspaces.id', auth()->user()->currentWorkspace->id);
        })
        ->firstOrFail();

        // update user role
        UpdateMemberRole::execute(auth()->user()->currentWorkspace, $user, $request->role);

        session()->flash('flash.banner', 'User role updated');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }

    /**
     * Remove user from workspace
     */
    public function destroy($id)
    {
        // validate if user exist on workspace
        $user = User::where('id', $id)
        ->whereHas('workspaces', function (Builder $query) {
            $query->where('workspaces.id', auth()->user()->currentWorkspace->id);
        })
        ->firstOrFail();

        RemoveMember::execute(auth()->user()->currentWorkspace, $user);

        session()->flash('flash.banner', 'User removed successful');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }

    public function leave()
    {
        $user = auth()->user();
        $workspace = auth()->user()->currentWorkspace;

        if ($workspace->users()->count() == 1) {
            session()->flash('flash.banner', 'The Team cannot stay without a user');
            session()->flash('flash.bannerStyle', 'danger');

            return back();
        }

        RemoveMember::execute($workspace, $user);

        $userWorkspaces = auth()->user()->workspaces();

        if ($userWorkspaces->count() >= 1) {
            $newWorkspace = Workspace::findOrFail($userWorkspaces->first()->id);
            if (!auth()->user()->switchWorkspace($newWorkspace)) {
                abort(403);
            }

            session()->flash('flash.banner', 'You leaved from team.');
            session()->flash('flash.bannerStyle', 'success');

            return redirect(route('links.index'));
        } else {
            auth()->user()->forceFill([
                'current_workspace_id' => null,
            ])->save();


            return redirect(route('workspaces.create'));
        }
    }
}
