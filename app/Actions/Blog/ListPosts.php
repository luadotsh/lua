<?php

declare(strict_types=1);

namespace App\Actions\Blog;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use SplFileInfo;

/**
 * Every post in `resources/blog`, newest first.
 *
 * The directory is the database: dropping a markdown file in it publishes a
 * post, and there is no index, registry or seeder to update alongside. That is
 * the whole point of the arrangement, so nothing here may require a second
 * step to keep in sync.
 */
class ListPosts
{
    /**
     * @return Collection<int, array<string, mixed>>
     */
    public static function execute(): Collection
    {
        return collect(self::files())
            ->map(fn (SplFileInfo $file): array => self::summarise($file))
            ->filter(fn (array $post): bool => self::isPublished($post))
            ->sortByDesc('date')
            ->values();
    }

    /**
     * One post by slug, with its rendered body — or null when there is no such
     * file, or it is not published yet.
     *
     * @return array<string, mixed>|null
     */
    public static function find(string $slug): ?array
    {
        $path = self::directory()."/{$slug}.md";

        // realpath resolves any `..` before the check, so a slug that climbs
        // out of the directory fails here rather than reading an arbitrary
        // file. The route pattern already excludes dots; this is the backstop.
        $real = realpath($path);
        $root = realpath(self::directory());

        if ($real === false || $root === false || ! str_starts_with($real, $root.DIRECTORY_SEPARATOR)) {
            return null;
        }

        $rendered = RenderPost::execute($real);
        $post = [
            ...self::meta($slug, $rendered['frontmatter'], $rendered['reading_time']),
            'html' => $rendered['html'],
            'headings' => $rendered['headings'],
        ];

        return self::isPublished($post) ? $post : null;
    }

    /**
     * A post dated in the future is a scheduled draft: it stays out of the
     * listing and 404s on its own URL until the day arrives. Writing ahead is
     * the normal way to work, so this has to hold on both surfaces.
     *
     * @param  array<string, mixed>  $post
     */
    private static function isPublished(array $post): bool
    {
        if (data_get($post, 'draft') === true) {
            return false;
        }

        $date = data_get($post, 'date');

        return $date !== null && Carbon::parse($date)->startOfDay()->lessThanOrEqualTo(Carbon::today());
    }

    /**
     * @return list<SplFileInfo>
     */
    private static function files(): array
    {
        if (! File::isDirectory(self::directory())) {
            return [];
        }

        return array_values(array_filter(
            File::files(self::directory()),
            fn (SplFileInfo $file): bool => $file->getExtension() === 'md',
        ));
    }

    /**
     * @return array<string, mixed>
     */
    private static function summarise(SplFileInfo $file): array
    {
        $rendered = RenderPost::execute((string) $file->getRealPath());

        return self::meta(
            Str::beforeLast($file->getFilename(), '.md'),
            $rendered['frontmatter'],
            $rendered['reading_time'],
        );
    }

    /**
     * @param  array<string, mixed>  $frontmatter
     * @return array<string, mixed>
     */
    private static function meta(string $slug, array $frontmatter, int $readingTime): array
    {
        return [
            'slug' => $slug,
            'title' => (string) data_get($frontmatter, 'title', Str::headline($slug)),
            'description' => (string) data_get($frontmatter, 'description', ''),
            // symfony/yaml resolves a bare `2026-08-20` to a Unix timestamp,
            // so it is normalised here rather than in each consumer.
            'date' => self::date($frontmatter),
            'author' => (string) data_get($frontmatter, 'author', 'Lua'),
            'image' => data_get($frontmatter, 'image'),
            'tags' => (array) data_get($frontmatter, 'tags', []),
            'draft' => (bool) data_get($frontmatter, 'draft', false),
            'reading_time' => $readingTime,
        ];
    }

    /**
     * @param  array<string, mixed>  $frontmatter
     */
    private static function date(array $frontmatter): ?string
    {
        $date = data_get($frontmatter, 'date');

        if ($date === null || $date === '') {
            return null;
        }

        return (is_int($date) ? Carbon::createFromTimestampUTC($date) : Carbon::parse((string) $date))
            ->toDateString();
    }

    private static function directory(): string
    {
        return resource_path('blog');
    }
}
