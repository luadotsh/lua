<?php

declare(strict_types=1);

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

/**
 * Same arrangement as the alternatives pages: `config/use_cases.php` holds the
 * content, the keys are the registry, and the template renders whatever shape
 * it finds. Adding an audience touches no route and no component.
 */
class UseCaseController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Site/UseCases/Index', [
            'useCases' => collect(config('use_cases'))
                ->map(fn (array $entry, string $slug): array => [
                    'slug' => $slug,
                    'name' => $entry['name'],
                    'intro' => $entry['intro'],
                ])
                ->values(),
            'seo' => [
                'title' => 'What people use Lua for',
                'description' => 'Campaigns, agencies, creators, print, newsletters and code. What each one needs from a short link, and where it stops helping.',
            ],
        ]);
    }

    public function show(string $slug): Response
    {
        $entry = config("use_cases.{$slug}");

        abort_unless(is_array($entry), HttpResponse::HTTP_NOT_FOUND);

        return Inertia::render('Site/UseCases/Show', [
            'slug' => $slug,
            'useCase' => $entry,
            // Three others to move on to, so a page is never a dead end.
            'others' => collect(config('use_cases'))
                ->except($slug)
                ->map(fn (array $other, string $otherSlug): array => [
                    'slug' => $otherSlug,
                    'name' => $other['name'],
                ])
                ->values()
                ->take(3),
            'seo' => $entry['seo'],
        ]);
    }
}
