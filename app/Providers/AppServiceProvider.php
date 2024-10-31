<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;

use Laravel\Cashier\Cashier;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

use App\Policies\WorkspacePolicy;

use App\Models\Workspace;
use App\Models\User;
use App\Models\ApiToken;
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
            'apiToken' => ApiToken::class,
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
}
