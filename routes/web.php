<?php


use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\AnalyticsController;

Route::group(
    [
        'middleware' => [
            'auth'
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


        // profile
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    }
);

require __DIR__.'/auth.php';

Route::get('/{key}', [LinkController::class, 'show'])->name('links.show');
