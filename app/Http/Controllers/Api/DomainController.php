<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Models\Domain;

class DomainController extends Controller
{
    public function validate(Request $request)
    {
        $request->validate([
            'domain' => ['required', 'max:255'],
        ]);

        $domain = Domain::where('domain', $request->domain)->first();
        if(!$domain) {
            return response()->json(['valid' => false], 404);
        }

        return response()->json(['valid' => true]);
    }
}
