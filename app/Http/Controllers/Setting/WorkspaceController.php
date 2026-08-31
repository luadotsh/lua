<?php

declare(strict_types=1);

namespace App\Http\Controllers\Setting;

use App\Actions\Media\DeleteMedia;
use App\Actions\Workspace\UpdateWorkspace;
use App\Http\Requests\Workspace\UpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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

        Gate::authorize('administer', $workspace);

        UpdateWorkspace::execute($workspace, $request->validated());

        session()->flash('flash.banner', 'Workspace updated');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }

    /**
     * The route has always existed; the method it named did not, so removing a
     * workspace logo was a BadMethodCallException.
     */
    public function deleteLogo(Request $request)
    {
        $workspace = $request->user()->currentWorkspace;

        Gate::authorize('administer', $workspace);

        $logo = $workspace->getFirstMedia('logo');

        if ($logo !== null) {
            DeleteMedia::execute($request->user(), $logo);
            $workspace->unsetRelation('media');
        }

        session()->flash('flash.banner', 'Logo removed');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }
}
