<?php

declare(strict_types=1);

namespace App\Http\Controllers\Setting;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Models\ApiToken;

class ApiTokenController extends Controller
{
    public function index()
    {
        $tokens = ApiToken::where('workspace_id', auth()->user()->current_workspace_id)->get();

        return Inertia::render('Setting/ApiToken/Index', [
            'tokens' => $tokens,
            'hasData' => $tokens->count() === 0 ? false : true
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string'
        ]);

        $token = ApiToken::create([
            'workspace_id' => auth()->user()->current_workspace_id,
            'name' => $request->name,
            'token' => Str::uuid()
        ]);

        return back()->with('flash', [
            'token' => $token->token
        ]);
    }

    public function destroy($id)
    {
        $token = ApiToken::where('workspace_id', auth()->user()->current_workspace_id)->findOrFail($id);
        $token->delete();

        session()->flash('flash.banner', 'API Token deleted successful.');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }
}
