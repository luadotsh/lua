<?php


use Illuminate\Support\Facades\Route;


use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\MediaController;

// setting
use App\Http\Controllers\Setting\AccountController;
use App\Http\Controllers\Setting\BillingController;
use App\Http\Controllers\Setting\DomainController;
use App\Http\Controllers\Setting\InviteController;
use App\Http\Controllers\Setting\TeamMemberController;

Route::group(
    [
        'middleware' => [
            'auth',
            'subscription-check'
        ],
    ],
    function () {

        // default route
        Route::get('/', function () {
            return redirect(route('links.index'));
        });

        // workspaces
        Route::get('/workspaces/create', [WorkspaceController::class, 'create'])->name('workspaces.create');
        Route::post('/workspaces', [WorkspaceController::class, 'store'])->name('workspaces.store');
        Route::put('/workspaces/update-current', [WorkspaceController::class, 'setCurrentStore'])->name('workspaces.update-current');

        // links
        Route::get('/links', [LinkController::class, 'index'])->name('links.index');
        Route::post('/links', [LinkController::class, 'store'])->name('links.store');
        Route::post('/links/{id}/duplicate', [LinkController::class, 'duplicate'])->name('links.duplicate');
        Route::post('/links', [LinkController::class, 'store'])->name('links.store');
        Route::get('/links/{id}', [LinkController::class, 'edit'])->name('links.edit');
        Route::put('/links/{id}', [LinkController::class, 'update'])->name('links.update');
        Route::delete('/links/{id}', [LinkController::class, 'destroy'])->name('links.destroy');

        // analytics
        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('/analytics/statistics', [AnalyticsController::class, 'statistics'])->name('analytics.statistics');

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


            // analytics
            Route::get('/domains', [DomainController::class, 'index'])->name('setting.domains.index');
            Route::post('/domains', [DomainController::class, 'statistics'])->name('setting.domains.store');

            // billing
            Route::get('/billing', [BillingController::class, 'index'])->name('setting.billing.index');
            Route::get('/billing/checkout/{stripeId}', [BillingController::class, 'checkout'])->name('setting.billing.checkout');
            Route::get('/billing/portal', [BillingController::class, 'billingPortal'])->name('setting.billing.portal');
            Route::get('/billing/swap-free-plan', [BillingController::class, 'swapFreePlan'])->name('setting.billing.swap-free-plan');
            Route::inertia('/billing/checkout-success', 'App/Setting/Billing/Success')->name('setting.billing.checkout-success');

            // users
            Route::get('/users', [TeamMemberController::class, 'index'])->name('setting.team-members.index');
            Route::put('/users/{id}/role', [TeamMemberController::class, 'updateUserRole'])->name('setting.team-members.role');
            Route::delete('/users/leave', [TeamMemberController::class, 'leave'])->name('setting.team-members.leave');
            Route::delete('/users/{id}/remove-from-team', [TeamMemberController::class, 'destroy'])->name('setting.team-members.destroy');

            // user invites
            Route::get('/users/invites/create', [InviteController::class, 'create'])->name('setting.invites.create');
            Route::post('/users/invites', [InviteController::class, 'store'])->name('setting.invites.store');
            Route::delete('/users/invites/{id}', [InviteController::class, 'destroy'])->name('setting.invites.destroy');
        });
    }
);

require __DIR__.'/auth.php';

Route::get('/{key}', [LinkController::class, 'show'])->name('links.show');
