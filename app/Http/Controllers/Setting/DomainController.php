<?php

namespace App\Http\Controllers\Setting;

use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Models\Domain;

class DomainController extends Controller
{
    public function index(Request $request)
    {
        $workspace = $request->user()->currentWorkspace;

        return Inertia::render('Domain/Index', [
            'table' => Domain::where('workspace_id', $workspace->id)->get(),
            'hasData' => Domain::where('workspace_id', $workspace->id)->exists(),
        ]);
    }

}
