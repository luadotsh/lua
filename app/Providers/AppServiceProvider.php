<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Domain;
use App\Models\Invite;
use App\Models\Link;
use App\Models\LinkStat;
use App\Models\Media;
use App\Models\Plan;
use App\Models\Tag;
use App\Models\User;
use App\Models\Workspace;
use App\Policies\WorkspacePolicy;
use App\Services\PostHogService;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use PostHog\PostHog;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureUrlScheme();

        // Cashier configuration
        Cashier::useCustomerModel(Workspace::class);

        // Analytics
        $this->configurePostHog();

        $this->configureRateLimiting();

        // Vite configuration
        Vite::prefetch(concurrency: 3);

        // Gate policies
        Gate::policy(Workspace::class, WorkspacePolicy::class);

        // Custom email verification template
        VerifyEmail::toMailUsing(function (User $user, string $url) {
            return (new MailMessage)
                ->subject('Verify Email Address')
                ->view('mail.email-verification', [
                    'title' => 'Confirm your email address',
                    'previewText' => 'Please confirm your email address.',
                    'user' => $user,
                    'url' => $url,
                ]);
        });

        // Morph map for polymorphic relationships
        Relation::enforceMorphMap([
            'domain' => Domain::class,
            'invite' => Invite::class,
            'link' => Link::class,
            'linkStat' => LinkStat::class,
            'media' => Media::class,
            'plan' => Plan::class,
            'tag' => Tag::class,
            'user' => User::class,
            'workspace' => Workspace::class,
        ]);
    }

    /**
     * The SDK is only initialised when a key is actually configured; every
     * call site is already gated on PostHogService, so a missing key means
     * nothing is sent rather than anything failing.
     */
    /**
     * Force the URL scheme to match APP_URL.
     *
     * Wayfinder writes an absolute URL for any route scoped with
     * Route::domain(), and the marketing site is: routes/site.php is bound to
     * config('domains.main'). Without a forced scheme it emits the
     * protocol-relative form — `//lua.sh/pricing` — because nothing has told
     * it which scheme applies (see Wayfinder's Route::uri()).
     *
     * Inertia then resolves that href against different bases on each side:
     * `http://localhost` on the server, window.location in the browser. On an
     * https site every link hydrates with a mismatch, one console warning per
     * link, because the server said http and the client says https.
     *
     * Deriving it from APP_URL fixes both ends at once and keeps http working
     * locally and in CI, where the app really is served over http.
     */
    protected function configureUrlScheme(): void
    {
        $scheme = parse_url((string) config('app.url'), PHP_URL_SCHEME);

        if (is_string($scheme) && $scheme !== '') {
            URL::forceScheme($scheme);
        }
    }

    protected function configurePostHog(): void
    {
        if (! PostHogService::isEnabled()) {
            return;
        }

        PostHog::init(config('services.posthog.api_key'), [
            'host' => config('services.posthog.host'),
        ]);
    }

    /**
     * Dynamic client registration is unauthenticated by design, so it gets a
     * tight limit of its own rather than sharing the global API budget.
     */
    protected function configureRateLimiting(): void
    {
        // Keyed by the token's workspace so one tenant cannot exhaust another's
        // budget, falling back to the IP for the unauthenticated routes.
        RateLimiter::for('api', function (Request $request) {
            if ($this->app->environment('local')) {
                return Limit::none();
            }

            return Limit::perMinute(60)->by($request->workspace?->id ?: $request->ip());
        });

        RateLimiter::for('mcp', function (Request $request) {
            if ($this->app->environment('local')) {
                return Limit::none();
            }

            return Limit::perMinute(120)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('mcp-oauth-registration', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });
    }
}
