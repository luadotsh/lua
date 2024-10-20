<?php

namespace App\Http\Controllers\Setting;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Enums\Domain\Status;

use App\Models\Domain;

class DomainController extends Controller
{
    public function index()
    {
        $domains = Domain::where('workspace_id', auth()->user()->currentWorkspace->id)->get();

        return Inertia::render('Setting/Domain/Index', [
            'domains' => $domains,
            'hasData' => $domains->count() === 0 ? false : true,
        ]);
    }

    public function store(Request $request)
    {

        $workspace = auth()->user()->currentWorkspace;

        $request->validate([
            'domain' => ['required', 'max:255', 'unique:domains,domain,NULL,id,workspace_id,' . $workspace->id],
        ]);

        Domain::create([
            'workspace_id' => auth()->user()->currentWorkspace->id,
            'domain' => $request->domain,
            'status' => Status::PENDING
        ]);

        session()->flash('flash.banner', 'Domain added successful.');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'domain' => ['required', 'max:255'],
        ]);

        $domain = Domain::where('id', $id)->where('workspace_id', auth()->user()->currentWorkspace->id)->firstOrFail();
        $domain->domain = $request->domain;
        $domain->status = Status::PENDING;
        $domain->save();

        session()->flash('flash.banner', 'Domain updated successful.');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }

    public function destroy($id)
    {
        $domain = Domain::where('workspace_id', auth()->user()->currentWorkspace->id)->where('id', $id)->firstOrFail();
        $domain->delete();

        session()->flash('flash.banner', 'Domain deleted successful.');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }
}
