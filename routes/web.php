<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\MediaController;

// setting
use App\Http\Controllers\Setting\AccountController;
use App\Http\Controllers\Setting\WorkspaceController as SettingWorkspaceController;
use App\Http\Controllers\Setting\UsageController;
use App\Http\Controllers\Setting\BillingController;
use App\Http\Controllers\Setting\DomainController;
use App\Http\Controllers\Setting\InviteController;
use App\Http\Controllers\Setting\TagController;
use App\Http\Controllers\Setting\ApiTokenController;
use App\Http\Controllers\Setting\TeamMemberController;

Route::group(
    [
        'middleware' => [
            'auth',
            'verified',
            'set-workspace',
            'billing'
        ],
    ],
    function () {

        // workspaces
        Route::get('/workspaces/create', [WorkspaceController::class, 'create'])->name('workspaces.create')->withoutMiddleware(['billing']);
        Route::post('/workspaces', [WorkspaceController::class, 'store'])->name('workspaces.store')->withoutMiddleware(['billing']);
        Route::put('/workspaces/update-current', [WorkspaceController::class, 'setCurrentStore'])->name('workspaces.update-current')->withoutMiddleware(['billing']);

        // links
        Route::get('/links/{id?}', [LinkController::class, 'index'])->name('links.index');
        Route::post('/links', [LinkController::class, 'store'])->name('links.store');
        Route::put('/links/{id}', [LinkController::class, 'update'])->name('links.update');
        Route::delete('/links/{id}', [LinkController::class, 'destroy'])->name('links.destroy');

        // analytics
        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('/analytics/statistics', [AnalyticsController::class, 'statistics'])->name('analytics.statistics');

        // events
        Route::get('/events', [EventController::class, 'index'])->name('events.index');

        // medias
        Route::get('/medias/{id}/download', [MediaController::class, 'download'])->name('medias.download')->withoutMiddleware('*');
        Route::post('/medias', [MediaController::class, 'store'])->name('medias.store');
        Route::post('/medias/sort', [MediaController::class, 'sort'])->name('medias.sort');
        Route::post('/medias/{modelId}/thumbnail/{id}', [MediaController::class, 'thumbnail'])->name('medias.thumbmail');
        Route::delete('/medias/{modelId}/{id}', [MediaController::class, 'destroy'])->name('medias.destroy');

        // settings
        Route::prefix('settings')->group(function () {

            // account
            Route::get('/account', [AccountController::class, 'edit'])->name('setting.account.edit')->withoutMiddleware(['set-store']);
            Route::post('/account', [AccountController::class, 'update'])->name('setting.account.update')->withoutMiddleware(['set-store']);
            Route::delete('/account/photo', [AccountController::class, 'deletePhoto'])->name('setting.account.photo.destroy')->withoutMiddleware(['set-store']);

            // workspace
            Route::get('/workspace', [SettingWorkspaceController::class, 'edit'])->name('setting.workspace.edit');
            Route::put('/workspace', [SettingWorkspaceController::class, 'update'])->name('setting.workspace.update');
            Route::delete('/workspace/photo', [SettingWorkspaceController::class, 'deleteLogo'])->name('setting.workspace.logo.destroy');

            // analytics
            Route::get('/domains', [DomainController::class, 'index'])->name('setting.domains.index');
            Route::post('/domains', [DomainController::class, 'store'])->name('setting.domains.store');
            Route::put('/domains/{id}', [DomainController::class, 'update'])->name('setting.domains.update');
            Route::delete('/domains/{id}', [DomainController::class, 'destroy'])->name('setting.domains.destroy');
            Route::get('/domains/{id}/validate-dns', [DomainController::class, 'validateDns'])->name('setting.domains.validate-dns');

            // billing
            Route::get('/billing', [BillingController::class, 'index'])->name('setting.billing.index');
            Route::get('/billing/upgrade', [BillingController::class, 'upgrade'])->name('setting.billing.upgrade');
            Route::get('/billing/checkout/{planId}', [BillingController::class, 'checkout'])->name('setting.billing.checkout');
            Route::get('/billing/portal', [BillingController::class, 'billingPortal'])->name('setting.billing.portal');
            Route::get('/billing/swap-free-plan', [BillingController::class, 'swapFreePlan'])->name('setting.billing.swap-free-plan');
            Route::inertia('/billing/checkout-success', 'Setting/Billing/Success')->name('setting.billing.checkout-success');

            // users
            Route::get('/users', [TeamMemberController::class, 'index'])->name('setting.team-members.index');
            Route::put('/users/{id}/role', [TeamMemberController::class, 'updateUserRole'])->name('setting.team-members.role');
            Route::delete('/users/leave', [TeamMemberController::class, 'leave'])->name('setting.team-members.leave');
            Route::delete('/users/{id}/remove-from-team', [TeamMemberController::class, 'destroy'])->name('setting.team-members.destroy');

            // user invites
            Route::get('/users/invites/create', [InviteController::class, 'create'])->name('setting.invites.create');
            Route::post('/users/invites', [InviteController::class, 'store'])->name('setting.invites.store');
            Route::delete('/users/invites/{id}', [InviteController::class, 'destroy'])->name('setting.invites.destroy');

            // tags
            Route::get('/tags', [TagController::class, 'index'])->name('setting.tags.index');
            Route::post('/tags', [TagController::class, 'store'])->name('setting.tags.store');
            Route::post('/tags/sort', [TagController::class, 'sort'])->name('setting.tags.sort');
            Route::put('/tags/{id}', [TagController::class, 'update'])->name('setting.tags.update');
            Route::delete('/tags/{id}', [TagController::class, 'destroy'])->name('setting.tags.destroy');

            // usage
            Route::get('/usage', [UsageController::class, 'index'])->name('setting.usage.index');

            // api tokens
            Route::get('/api-tokens', [ApiTokenController::class, 'index'])->name('setting.api-tokens.index');
            Route::post('/api-tokens', [ApiTokenController::class, 'store'])->name('setting.api-tokens.store');
            Route::delete('/api-tokens/{id}', [ApiTokenController::class, 'destroy'])->name('setting.api-tokens.destroy');
        });
    }
);

require __DIR__.'/auth.php';

// links
Route::get('/{key}/password', [RedirectController::class, 'password'])->name('links.password')->middleware(['custom-domain']);
Route::post('/{key}/password', [RedirectController::class, 'validatePassword'])->name('links.password.validate')->middleware(['custom-domain']);
Route::get('/{key?}', [RedirectController::class, 'redirect'])->name('links.redirect')->middleware(['custom-domain']);
