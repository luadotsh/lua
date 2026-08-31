<?php

declare(strict_types=1);

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

/**
 * Definitions of the terms the rest of the site uses. Same arrangement as
 * alternatives and use cases: `config/glossary.php` is the registry.
 */
class GlossaryController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Site/Glossary/Index', [
            // Grouped by first letter, which is the one ordering a glossary
            // reader expects and the only one they can navigate by.
            'letters' => collect(config('glossary'))
                ->map(fn (array $entry, string $slug): array => [
                    'slug' => $slug,
                    'term' => $entry['term'],
                    'short' => $entry['short'],
                ])
                ->sortBy(fn (array $entry): string => Str::lower($entry['term']))
                ->groupBy(fn (array $entry): string => Str::upper(Str::substr($entry['term'], 0, 1)))
                ->map(fn ($entries, string $letter): array => [
                    'letter' => $letter,
                    'terms' => $entries->values(),
                ])
                ->values(),
            'seo' => [
                'title' => 'Glossary of link and click analytics terms',
                'description' => 'UTM parameters, referrers, redirects, link rot and the rest, defined by how the web actually behaves rather than how a marketing page wishes it did.',
            ],
        ]);
    }

    public function show(string $slug): Response
    {
        $entry = config("glossary.{$slug}");

        abort_unless(is_array($entry), HttpResponse::HTTP_NOT_FOUND);

        return Inertia::render('Site/Glossary/Show', [
            'slug' => $slug,
            'entry' => $entry,
            'related' => collect($entry['related'] ?? [])
                ->map(fn (string $relatedSlug): ?array => is_array(config("glossary.{$relatedSlug}"))
                    ? ['slug' => $relatedSlug, 'term' => config("glossary.{$relatedSlug}.term"), 'short' => config("glossary.{$relatedSlug}.short")]
                    : null)
                ->filter()
                ->values(),
            'seo' => [
                'title' => $entry['term'],
                'description' => $entry['short'],
            ],
        ]);
    }
}
