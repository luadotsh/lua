<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\WebsiteController;

Route::group(
    [

        'middleware' => [

        ],
    ],
    function () {

        // stores
        Route::get('/websites/favicon', [WebsiteController::class, 'favicon'])->name('websites.favicon');
    }
);
