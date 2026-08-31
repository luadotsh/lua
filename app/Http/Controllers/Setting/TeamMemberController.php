<?php

declare(strict_types=1);

namespace App\Http\Controllers\Setting;

use App\Actions\Invite\ListInvites;
use App\Actions\TeamMember\LeaveWorkspace;
use App\Actions\TeamMember\ListMembers;
use App\Actions\TeamMember\RemoveMember;
use App\Actions\TeamMember\UpdateMemberRole;
use App\Http\Requests\TeamMember\UpdateUserRoleRequest;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class TeamMemberController extends Controller
{
    public function index(Request $request)
    {
        $workspace = auth()->user()->currentWorkspace;

        return Inertia::render('Setting/TeamMember/Index', [
            // is_owner rather than a role: the screen hides the controls the
            // policy would refuse, and ownership is what it refuses on.
            'users' => ListMembers::execute($workspace, ['search' => $request->q])
                ->each(fn ($member) => $member->is_owner = $member->id === $workspace->owner_id),
            'invites' => ListInvites::execute($workspace),
        ]);
    }

    public function updateUserRole($id, UpdateUserRoleRequest $request)
    {
        $workspace = auth()->user()->currentWorkspace;

        // validate user
        $user = User::where('id', $id)
            ->whereHas('workspaces', function (Builder $query) {
                $query->where('workspaces.id', auth()->user()->currentWorkspace->id);
            })
            ->firstOrFail();

        Gate::authorize('manageMember', [$workspace, $user]);

        UpdateMemberRole::execute($workspace, $user, $request->role);

        session()->flash('flash.banner', 'User role updated');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }

    /**
     * Remove user from workspace
     */
    public function destroy($id)
    {
        $workspace = auth()->user()->currentWorkspace;

        // validate if user exist on workspace
        $user = User::where('id', $id)
            ->whereHas('workspaces', function (Builder $query) {
                $query->where('workspaces.id', auth()->user()->currentWorkspace->id);
            })
            ->firstOrFail();

        Gate::authorize('manageMember', [$workspace, $user]);

        RemoveMember::execute($workspace, $user);

        session()->flash('flash.banner', 'User removed successful');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }

    public function leave()
    {
        $user = auth()->user();
        $workspace = $user->currentWorkspace;

        // The owner cannot walk away from what they answer for; they hand it
        // on or close it.
        if ($user->ownsWorkspace($workspace)) {
            session()->flash('flash.banner', 'Transfer ownership before leaving this workspace.');
            session()->flash('flash.bannerStyle', 'danger');

            return back();
        }

        if (LeaveWorkspace::isLastMember($workspace)) {
            session()->flash('flash.banner', 'The Team cannot stay without a user');
            session()->flash('flash.bannerStyle', 'danger');

            return back();
        }

        if (LeaveWorkspace::execute($user, $workspace)) {
            session()->flash('flash.banner', 'You left the team.');
            session()->flash('flash.bannerStyle', 'success');

            return redirect(route('links.index'));
        }

        return redirect(route('workspaces.create'));
    }
}
