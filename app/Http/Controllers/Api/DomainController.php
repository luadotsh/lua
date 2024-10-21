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
            return response()->json(['valid' => false, 'error' => 'Domain not found'], 404);
        }

        // Check if domain has valid DNS records
        $isValidDomain = fn($domain) => checkdnsrr($domain, 'CNAME') || checkdnsrr($domain, 'A');
        $isValidCnameTarget = fn($dnsRecord) => $dnsRecord && strpos($dnsRecord[0]['target'], config('domains.cname')) !== false;

        if ($isValidDomain($domain)) {
            $dnsRecord = dns_get_record($domain, DNS_CNAME);

            return $isValidCnameTarget($dnsRecord)
                ? response()->json(['valid' => true], 200)
                : response()->json([
                    'valid' => false,
                    'error' => 'Please add a CNAME record with the following target: ' . config('domains.cname')
                ], 400);
        }

        return response()->json(['valid' => false, 'error' => 'Domain has no valid DNS records'], 400);
    }
}
