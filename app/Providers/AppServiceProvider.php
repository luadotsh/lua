<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;

use Laravel\Cashier\Cashier;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

use PostHog\PostHog;

use App\Services\PostHogService;

use App\Policies\WorkspacePolicy;

use App\Models\Workspace;
use App\Models\User;
use App\Models\Domain;
use App\Models\Invite;
use App\Models\Link;
use App\Models\LinkStat;
use App\Models\Media;
use App\Models\Plan;
use App\Models\Tag;


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
                    'url' => $url
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
