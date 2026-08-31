<?php

declare(strict_types=1);

namespace App\Http\Controllers\Setting;

use App\Actions\Domain\CreateDomain;
use App\Actions\Domain\DeleteDomain;
use App\Actions\Domain\GetDomain;
use App\Actions\Domain\ListDomains;
use App\Actions\Domain\UpdateDomain;
use App\Actions\Domain\VerifyDomainDns;
use App\Http\Requests\Domain\CreateRequest;
use App\Http\Requests\Domain\UpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class DomainController extends Controller
{
    public function index()
    {
        $domains = ListDomains::execute(Auth::user()->currentWorkspace);

        return Inertia::render('Setting/Domain/Index', [
            'domains' => $domains,
            'hasData' => $domains->count() === 0 ? false : true,
        ]);
    }

    public function store(CreateRequest $request)
    {
        $workspace = Auth::user()->currentWorkspace;

        $response = Gate::inspect('reached-domain-limit', $workspace);
        if (! $response->allowed()) {
            session()->flash('flash.banner', 'You have reached the limit of domains, please upgrade your plan.');
            session()->flash('flash.bannerStyle', 'danger');

            return back();
        }

        CreateDomain::execute($workspace, $request->validated());

        session()->flash('flash.banner', 'Domain added successful.');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }

    public function update(UpdateRequest $request, $id)
    {
        $domain = GetDomain::execute(Auth::user()->currentWorkspace, $id);
        abort_unless($domain, 404);

        UpdateDomain::execute($domain, $request->validated());

        session()->flash('flash.banner', 'Domain updated successful.');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }

    public function destroy($id)
    {
        $domain = GetDomain::execute(Auth::user()->currentWorkspace, $id);
        abort_unless($domain, 404);

        DeleteDomain::execute($domain);

        session()->flash('flash.banner', 'Domain deleted successful.');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }

    public function validateDns($id, Request $request)
    {
        $workspace = Auth::user()->currentWorkspace;

        $domain = GetDomain::execute($workspace, $id);
        abort_unless($domain, 404);

        if (! VerifyDomainDns::execute($domain)) {
            session()->flash('flash.banner', 'The domain does not have a CNAME record pointing to '.config('domains.cname').'.');
            session()->flash('flash.bannerStyle', 'danger');

            return back();
        }

        session()->flash('flash.banner', 'Domain validated successful.');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }
}
