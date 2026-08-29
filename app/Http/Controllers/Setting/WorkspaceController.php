<?php

declare(strict_types=1);

namespace App\Http\Controllers\Setting;

use App\Actions\Workspace\UpdateWorkspace;
use Illuminate\Http\Request;

use App\Http\Requests\Workspace\UpdateRequest;

use Inertia\Inertia;

class WorkspaceController extends Controller
{
    public function edit()
    {
        return Inertia::render('Setting/Workspace/Edit');
    }

    public function update(UpdateRequest $request)
    {
        $workspace = auth()->user()->currentWorkspace;

        UpdateWorkspace::execute($workspace, $request->validated());

        session()->flash('flash.banner', 'Workspace updated');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }
}
