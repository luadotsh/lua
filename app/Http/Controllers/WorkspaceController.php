<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Inertia\Inertia;
use Inertia\Response;

use App\Actions\Workspace\CreateWorkspace;

use App\Models\Workspace;

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

        try {
            CreateWorkspace::execute(auth()->user(), [
                'name' => $request->name,
            ]);

            return redirect(route('links.index'));
        } catch (\Exception $e) {
            Log::error($e);

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
