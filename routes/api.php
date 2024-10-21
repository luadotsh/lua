<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\WebsiteController;
use App\Http\Controllers\Api\LinkController;
use App\Http\Controllers\Api\DomainController;

Route::group(['middleware' => ['api.auth']], function () {

        // websites
        Route::get('/websites/favicon', [WebsiteController::class, 'favicon'])->name('websites.favicon')->withoutMiddleware('api.auth');

        // links
        Route::get('/links', [LinkController::class, 'index'])->name('api.links.index');
        Route::post('/links', [LinkController::class, 'store'])->name('api.links.store');
        Route::put('/links/{link}', [LinkController::class, 'update'])->name('api.links.update');
        Route::delete('/links/{link}', [LinkController::class, 'destroy'])->name('api.links.destroy');

        // domains
        Route::get('/domains/validate', [DomainController::class, 'validate'])->name('api.domains.validate')->withoutMiddleware('api.auth');
    }
);
