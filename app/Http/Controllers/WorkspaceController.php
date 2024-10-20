<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Inertia\Inertia;
use Inertia\Response;

use App\Enums\User\Role;

use App\Models\Workspace;
use App\Models\Plan;

class WorkspaceController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Workspace/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        DB::beginTransaction();

        try {
            $user = auth()->user();

            $workspace = Workspace::create([
                'name' => $request->name,
                'plan_id' => Plan::where('internal_id', 'free')->first()->id,
                'billing_cycle_start' => now()->day,
            ]);

            // attach user to project
            $user->workspaces()->attach($workspace->id, [
                'role' => Role::ROLE_OWNER,
            ]);

            $user->forceFill([
                'current_workspace_id' => $workspace->id,
            ])->save();

            DB::commit();

            return redirect(route('links.index'));
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();

            session()->flash('flash.banner', 'Error creating workspace');
            session()->flash('flash.bannerStyle', 'danger');
            return back();
        }
    }

    public function setCurrentStore(Request $request)
    {
        $workspace = Workspace::findOrFail($request->workspace_id);

        if (!$request->user()->switchWorkspace($workspace)) {
            abort(403);
        }

        return Inertia::location(route('links.index'));
    }
}
