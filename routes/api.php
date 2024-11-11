<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\WebsiteController;
use App\Http\Controllers\Api\LinkController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\DomainController;
use App\Http\Controllers\Api\QrcodeController;

Route::group(['middleware' => ['api.auth']], function () {

        // favicon
        Route::get('/websites/favicon', WebsiteController::class)->name('websites.favicon')->withoutMiddleware('api.auth');

        // links
        Route::get('/links', [LinkController::class, 'index'])->name('api.links.index');
        Route::get('/links/{id}', [LinkController::class, 'show'])->name('api.links.show');
        Route::post('/links', [LinkController::class, 'store'])->name('api.links.store');
        Route::put('/links/{id}', [LinkController::class, 'update'])->name('api.links.update');
        Route::delete('/links/{id}', [LinkController::class, 'destroy'])->name('api.links.destroy');

        // qr-code
        Route::get('/links/{id}/qr-code', QrcodeController::class)->name('api.qr-code')->withoutMiddleware('api.auth');

        // tags
        Route::get('/tags', [TagController::class, 'index'])->name('api.tags.index');
        Route::post('/tags', [TagController::class, 'store'])->name('api.tags.store');
        Route::put('/tags/{id}', [TagController::class, 'update'])->name('api.tags.update');
        Route::delete('/tags/{id}', [TagController::class, 'destroy'])->name('api.tags.destroy');

        // domains
        Route::get('/domains/validate', [DomainController::class, 'validate'])->name('api.domains.validate')->withoutMiddleware('api.auth');
        Route::get('/domains', [DomainController::class, 'index'])->name('api.domains.index');
        Route::post('/domains', [DomainController::class, 'store'])->name('api.domains.store');
        Route::put('/domains/{id}', [DomainController::class, 'update'])->name('api.domains.update');
        Route::delete('/domains/{id}', [DomainController::class, 'destroy'])->name('api.domains.destroy');
    }
);
