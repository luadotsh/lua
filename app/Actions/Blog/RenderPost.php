<?php

declare(strict_types=1);

namespace App\Actions\Blog;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;

/**
 * Turn one `resources/blog/<slug>.md` file into the three things a post page
 * needs: its frontmatter, its HTML, and the list of headings the table of
 * contents scrolls between.
 *
 * The heading ids are added here rather than in the browser, so the same pass
 * that writes an `id` into the HTML is the one that reports it to the TOC.
 * Two separate slug functions is how a contents list ends up linking to
 * anchors that do not exist.
 */
class RenderPost
{
    /**
     * @return array{frontmatter: array<string, mixed>, html: string, headings: list<array{id: string, text: string, level: int}>, reading_time: int}
     */
    public static function execute(string $path): array
    {
        // The modified time is part of the key, so editing a post invalidates
        // its own entry and nothing has to be cleared by hand.
        $mtime = (int) filemtime($path);

        return Cache::rememberForever("blog:{$path}:{$mtime}", function () use ($path): array {
            $environment = new Environment(['html_input' => 'strip']);
            $environment->addExtension(new CommonMarkCoreExtension);
            $environment->addExtension(new GithubFlavoredMarkdownExtension);
            $environment->addExtension(new FrontMatterExtension);

            $raw = (string) file_get_contents($path);
            $result = (new MarkdownConverter($environment))->convert($raw);

            $frontmatter = $result instanceof RenderedContentWithFrontMatter
                ? (array) $result->getFrontMatter()
                : [];

            [$html, $headings] = self::anchorHeadings($result->getContent());

            return [
                'frontmatter' => $frontmatter,
                'html' => $html,
                'headings' => $headings,
                'reading_time' => self::readingTime($raw),
            ];
        });
    }

    /**
     * Give every h2 and h3 an id and report them in document order.
     *
     * Deeper levels are left alone: a contents list that includes every h4 is
     * an outline of the article rather than a way to move around it.
     *
     * @return array{0: string, 1: list<array{id: string, text: string, level: int}>}
     */
    private static function anchorHeadings(string $html): array
    {
        $headings = [];
        $seen = [];

        $html = (string) preg_replace_callback(
            '/<h([23])>(.*?)<\/h\1>/s',
            function (array $match) use (&$headings, &$seen): string {
                $level = (int) $match[1];
                $text = trim(html_entity_decode(strip_tags($match[2]), ENT_QUOTES | ENT_HTML5));
                $id = Str::slug($text) ?: 'section';

                // Two headings with the same words would otherwise both answer
                // to the same anchor, and the second would be unreachable.
                $seen[$id] = ($seen[$id] ?? 0) + 1;

                if ($seen[$id] > 1) {
                    $id = "{$id}-{$seen[$id]}";
                }

                $headings[] = ['id' => $id, 'text' => $text, 'level' => $level];

                return "<h{$level} id=\"{$id}\">{$match[2]}</h{$level}>";
            },
            $html,
        );

        return [$html, $headings];
    }

    /**
     * Minutes, at the 200 words a minute people generally read prose at.
     */
    private static function readingTime(string $markdown): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags($markdown)) / 200));
    }
}
