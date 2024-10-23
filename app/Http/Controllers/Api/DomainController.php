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
        ], $request->query());



        // Check if the domain is either in the available domains list or in the database
        $isInConfig = in_array($request->input('domain'), config('domains.available'));

        dd($isInConfig);
        $isInDatabase = Domain::where('domain', $request->input('domain'))->exists();

        // If the domain is found in either config or database, return valid: true
        if ($isInConfig || $isInDatabase) {
            return response()->json(['valid' => true]);
        }

        // If not found, return valid: false
        return response()->json(['valid' => false], 404);
    }
}
