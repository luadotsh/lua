<?php

declare(strict_types=1);

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class AlternativeController extends Controller
{
    /**
     * The competitor pages are pure content: config/alternatives.php holds one
     * entry per rival and the Vue template renders whatever shape it finds, so
     * adding a comparison touches no component and no route.
     *
     * The config's own keys are the registry, which is what keeps a page from
     * going live and staying orphaned from this list.
     */
    public function index(): Response
    {
        return Inertia::render('Site/Alternatives/Index', [
            'alternatives' => collect(config('alternatives'))
                ->map(fn (array $entry, string $slug) => [
                    'slug' => $slug,
                    'name' => $entry['name'],
                    'intro' => $entry['intro'],
                ])
                ->values(),
            'seo' => [
                'title' => 'Alternatives, compared honestly',
                'description' => 'How Lua compares to Bitly and the rest: pricing, what each does better, and who should actually switch.',
            ],
        ]);
    }

    public function show(string $slug): Response
    {
        $entry = config("alternatives.{$slug}");

        abort_unless(is_array($entry), HttpResponse::HTTP_NOT_FOUND);

        return Inertia::render('Site/Alternatives/Show', [
            'slug' => $slug,
            'alternative' => $entry,
            'seo' => $entry['seo'],
        ]);
    }
}
