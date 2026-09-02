<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\Auth\SocialAuthProvider;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     */
    public function rootView(Request $request): string
    {
        return 'app';
    }

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => function () use ($request) {
                    if (! $request->user()) {
                        return;
                    }

                    $currentWorkspace = $request->user()->current_workspace_id ? $request->user()->currentWorkspace : null;
                    if ($currentWorkspace) {
                        $currentWorkspace->role = $request->user()->workspaceRole($currentWorkspace);
                        // Ownership is a column, not a role, so it travels
                        // alongside rather than inside it.
                        $currentWorkspace->is_owner = $request->user()->ownsWorkspace($currentWorkspace);
                    }

                    $request->user()->loadMissing('media');
                    $currentWorkspace?->loadMissing('media');

                    return array_merge($request->user()->toArray(), array_filter([
                        'current_workspace' => $currentWorkspace,
                        'workspaces' => $request->user()->workspaces,
                    ]));
                },
            ],
            'socialProviders' => collect(SocialAuthProvider::enabled())
                ->map(fn (SocialAuthProvider $provider) => [
                    'provider' => $provider->value,
                    'label' => $provider->label(),
                ])->values(),
            // The analytics map needs this in the browser; sharing it here
            // keeps the token in config rather than in a VITE_ mirror.
            'mapboxToken' => config('services.mapbox.token'),
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'flash' => $request->session()->get('flash', []),
            'env' => config('app.env'),
            // The marketing pages build canonical and OG urls from this, and
            // a relative one is no use to a crawler or a link preview.
            'appUrl' => rtrim((string) config('app.url'), '/'),
            // The marketing site now lives in a separate project; this is
            // how the signed-in app links out to it (e.g. terms, privacy).
            'website' => rtrim((string) config('app.website'), '/'),
            'locale' => app()->getLocale(),
        ];
    }
}
