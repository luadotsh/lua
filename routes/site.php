<?php

declare(strict_types=1);

use App\Http\Controllers\Site\AlternativeController;
use App\Http\Controllers\Site\BlogController;
use App\Http\Controllers\Site\GlossaryController;
use App\Http\Controllers\Site\PageController;
use App\Http\Controllers\Site\ToolController;
use App\Http\Controllers\Site\UseCaseController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Marketing site
|--------------------------------------------------------------------------
|
| Scoped to the main domain on purpose. A customer's own domain is for their
| short links and nothing else: links.acme.com/pricing has to resolve as
| their link named "pricing", not serve ours.
|
| Registered before the {key?} catch-all in web.php, so these paths win over
| a short link of the same name. That makes them reserved words, which
| CreateLink refuses at creation rather than letting someone make a link
| that could never resolve.
|
*/

Route::domain((string) config('domains.main'))->group(function (): void {
    Route::get('/', [PageController::class, 'home'])->name('site.home');
    Route::get('/pricing', [PageController::class, 'pricing'])->name('site.pricing');
    Route::get('/faq', [PageController::class, 'faq'])->name('site.faq');
    Route::get('/terms', [PageController::class, 'terms'])->name('site.terms');
    Route::get('/privacy', [PageController::class, 'privacy'])->name('site.privacy');

    Route::get('/blog', [BlogController::class, 'index'])->name('site.blog.index');
    // The slug is a filename under resources/blog, so the pattern has to keep
    // out anything that could climb out of that directory.
    Route::get('/blog/{slug}', [BlogController::class, 'show'])
        ->where('slug', '[a-z0-9-]+')
        ->name('site.blog.show');

    Route::get('/use-cases', [UseCaseController::class, 'index'])->name('site.use-cases.index');
    Route::get('/use-cases/{slug}', [UseCaseController::class, 'show'])
        ->where('slug', '[a-z0-9-]+')
        ->name('site.use-cases.show');

    Route::get('/glossary', [GlossaryController::class, 'index'])->name('site.glossary.index');
    Route::get('/glossary/{slug}', [GlossaryController::class, 'show'])
        ->where('slug', '[a-z0-9-]+')
        ->name('site.glossary.show');

    Route::get('/tools', [ToolController::class, 'index'])->name('site.tools.index');
    Route::get('/tools/utm-builder', [ToolController::class, 'utmBuilder'])->name('site.tools.utm-builder');
    Route::get('/tools/qr-generator', [ToolController::class, 'qrGenerator'])->name('site.tools.qr-generator');
    Route::get('/tools/link-checker', [ToolController::class, 'linkChecker'])->name('site.tools.link-checker');
    // The only tool that touches the network, so the only one that needs a
    // limit: it fetches a URL a stranger chose, from our server.
    Route::post('/tools/link-checker', [ToolController::class, 'check'])
        ->middleware('throttle:10,1')
        ->name('site.tools.check');

    Route::get('/alternatives', [AlternativeController::class, 'index'])->name('site.alternatives.index');
    // The slug is read back with config() dot notation, so the pattern has to
    // exclude the dot: `bitly.name` would otherwise resolve to a nested value.
    Route::get('/alternatives/{slug}', [AlternativeController::class, 'show'])
        ->where('slug', '[a-z0-9-]+')
        ->name('site.alternatives.show');
});
