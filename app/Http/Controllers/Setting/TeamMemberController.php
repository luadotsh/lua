<?php

namespace App\Http\Controllers\Setting;

use App\Http\Requests\TeamMember\UpdateUserRoleRequest;

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;

use App\Enums\User\Role as UserRole;

use App\Models\Project;
use App\Models\User;
use App\Models\Invite;

class TeamMemberController extends Controller
{
    public function index(Request $request)
    {
        $project = Project::where('id', auth()->user()->currentProject->id)
            ->with('users', function ($query) {
                $query->orderBy('name', 'asc');
                $query->where('role', '!=', UserRole::ROLE_USER);
            })->first();

        $invites = Invite::where('project_id', auth()->user()->currentProject->id)->get();

        return Inertia::render('App/Setting/TeamMember/Index', [
            'users' => $project->users,
            'invites' => $invites
        ]);
    }

    public function updateUserRole($id, UpdateUserRoleRequest $request)
    {
        Gate::forUser(auth()->user())->authorize('updateProjectMember', auth()->user()->currentProject);

        // validate user
        $user = User::where('id', $id)
        ->whereHas('projects', function (Builder $query) {
            $query->where('projects.id', auth()->user()->currentProject->id);
        })
        ->first();

        if (!$user) {
            abort(404);
        }

        // update user role
        $user->projects()->syncWithPivotValues([auth()->user()->currentProject->id], ['role' => $request->role], false);

        session()->flash('flash.banner', 'User role updated');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }

    /**
     * Remove user from project
     */
    public function destroy($id)
    {
        Gate::forUser(auth()->user())->authorize('removeProjectMember', auth()->user()->currentProject);

        // validate if user exist on project
        $user = User::where('id', $id)
        ->whereHas('projects', function (Builder $query) {
            $query->where('projects.id', auth()->user()->currentProject->id);
        })
        ->firstOrFail();

        // detach user from project
        $user->projects()->detach(auth()->user()->currentProject->id);

        session()->flash('flash.banner', 'User removed successful');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }

    public function leave()
    {
        $user = auth()->user();
        $project = auth()->user()->currentProject;

        if ($project->users()->count() == 1) {
            session()->flash('flash.banner', 'The Team cannot stay without a user');
            session()->flash('flash.bannerStyle', 'danger');

            return back();
        }

        $user->projects()->detach($project->id);

        $userProjects = auth()->user()->projects();

        if ($userProjects->count() >= 1) {
            $newProject = Project::findOrFail($userProjects->first()->id);
            if (!auth()->user()->switchProject($newProject)) {
                abort(403);
            }

            session()->flash('flash.banner', 'You leaved from team.');
            session()->flash('flash.bannerStyle', 'success');

            return redirect(route('changelogs.index'));
        } else {
            auth()->user()->forceFill([
                'current_project_id' => null,
            ])->save();


            return redirect(route('projects.create'));
        }
    }
}
