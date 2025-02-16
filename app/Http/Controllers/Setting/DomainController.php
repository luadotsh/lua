<?php

declare(strict_types=1);

namespace App\Http\Controllers\Setting;

use App\Http\Requests\Domain\CreateRequest;
use App\Http\Requests\Domain\UpdateRequest;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Enums\Domain\Status;

use App\Models\Domain;

class DomainController extends Controller
{
    public function index()
    {
        $domains = Domain::where('workspace_id', Auth::user()->currentWorkspace->id)->get();

        return Inertia::render('Setting/Domain/Index', [
            'domains' => $domains,
            'hasData' => $domains->count() === 0 ? false : true,
        ]);
    }

    public function store(CreateRequest $request)
    {
        $workspace = Auth::user()->currentWorkspace;

        $response = Gate::inspect('reached-domain-limit', $workspace);
        if (!$response->allowed()) {
            session()->flash('flash.banner', 'You have reached the limit of domains, please upgrade your plan.');
            session()->flash('flash.bannerStyle', 'danger');
            return back();
        }

        Domain::create([
            'workspace_id' => $workspace->id,
            'domain' => $request->domain,
            'status' => Status::PENDING,
            'not_found_url' => $request->not_found_url,
            'expired_url' => $request->expired_url,
        ]);

        session()->flash('flash.banner', 'Domain added successful.');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }

    public function update(UpdateRequest $request, $id)
    {
        $domain = Domain::where('id', $id)->where('workspace_id', Auth::user()->currentWorkspace->id)->firstOrFail();
        $domain->domain = $request->domain;
        $domain->not_found_url = $request->not_found_url;
        $domain->expired_url = $request->expired_url;
        $domain->save();

        if($domain->wasChanged('domain')) {
            $domain->status = Status::PENDING;
            $domain->save();
        }

        session()->flash('flash.banner', 'Domain updated successful.');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }

    public function destroy($id)
    {
        $domain = Domain::where('workspace_id', Auth::user()->currentWorkspace->id)->where('id', $id)->firstOrFail();
        $domain->delete();

        session()->flash('flash.banner', 'Domain deleted successful.');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }

    public function validateDns($id, Request $request)
    {
        $workspace = Auth::user()->currentWorkspace;

        $domain = Domain::where('workspace_id', $workspace->id)->where('id', $id)->firstOrFail();

        // Use dns_get_record to fetch CNAME records
        $dnsRecords = dns_get_record($domain->domain, DNS_CNAME);

        // Check if dns_get_record returned false (an error occurred)
        if ($dnsRecords === false) {
            session()->flash('flash.banner', 'Unable to fetch DNS records for the domain.');
            session()->flash('flash.bannerStyle', 'danger');
            return back();
        }

        // Check if any CNAME record points to 'cname.lua.sh'
        $valid = false;
        foreach ($dnsRecords as $record) {
            if (
                isset($record['host'], $record['target']) && // Check if 'host' and 'target' keys exist
                $record['host'] === $domain->domain && // Check if 'host' matches the domain
                $record['target'] === config('domains.cname') // Check if 'target' matches the CNAME
            ) {
                $valid = true;
                break;
            }
        }

        if(!$valid) {
            session()->flash('flash.banner', 'The domain does not have a CNAME record pointing to cname.lua.sh.');
            session()->flash('flash.bannerStyle', 'danger');
            return back();
        }

        $domain->status = Status::ACTIVE;
        $domain->save();

        session()->flash('flash.banner', 'Domain validated successful.');
        session()->flash('flash.bannerStyle', 'success');
        return back();
    }
}
