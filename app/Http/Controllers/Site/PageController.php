<?php

declare(strict_types=1);

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function home(): Response
    {
        return Inertia::render('Site/Home', [
            // The reader is choosing between products, so the home page names
            // the others rather than pretending it is the only one.
            'alternatives' => collect(config('alternatives'))
                ->map(fn (array $entry, string $slug): array => ['slug' => $slug, 'name' => $entry['name']])
                ->values(),
            'faq' => self::faqGroups(homeOnly: true),
            'seo' => [
                'title' => 'Short links, and the story of every click',
                'description' => 'Put a short link on your own domain, then see what happened after it: country, device, browser, referrer and campaign. Open source and self-hostable.',
            ],
        ]);
    }

    /**
     * Prices come from the plans table rather than from the page, so what
     * someone is quoted here is what they are charged at checkout.
     *
     * Monthly and yearly rows are paired by `access_level` and shaped into one
     * tier each, so the toggle on the page swaps a number instead of the
     * component re-deciding what a plan is.
     */
    public function pricing(): Response
    {
        $tiers = Plan::where('is_private', false)
            ->orderBy('access_level')
            ->get()
            ->groupBy('access_level')
            ->map(function ($plans) {
                $monthly = $plans->firstWhere('is_monthly', true);
                $yearly = $plans->firstWhere('is_monthly', false);

                if ($monthly === null) {
                    return null;
                }

                return [
                    'name' => $monthly->name,
                    'internal_id' => $monthly->internal_id,
                    'monthly_price' => (int) $monthly->price,
                    // A tier with no yearly row simply has no yearly price;
                    // the page falls back to the monthly one rather than
                    // inventing a discount.
                    'yearly_price' => $yearly ? (int) $yearly->price : null,
                    'max_links' => (int) $monthly->max_links,
                    'max_events' => (int) $monthly->max_events,
                    'max_users' => (int) $monthly->max_users,
                    'max_tags' => (int) $monthly->max_tags,
                    'max_domains' => (int) $monthly->max_domains,
                ];
            })
            ->filter()
            ->values();

        return Inertia::render('Site/Pricing', [
            'tiers' => $tiers,
            'seo' => [
                'title' => 'Pricing',
                'description' => 'Start free with 5 links. Paid plans add custom domains, your team, and the click history to go with them.',
            ],
        ]);
    }

    /**
     * The whole list. The home page takes a slice of the same config, so the
     * two can never disagree about an answer.
     */
    public function faq(): Response
    {
        return Inertia::render('Site/Faq', [
            'groups' => self::faqGroups(),
            'seo' => [
                'title' => 'Frequently asked questions',
                'description' => 'What Lua records, how accurate it is, what is in the free plan, and what happens to your links if you leave.',
            ],
        ]);
    }

    /**
     * @return list<array{title: string, items: list<array{question: string, answer: string}>}>
     */
    public static function faqGroups(bool $homeOnly = false): array
    {
        return collect(config('faq'))
            ->map(fn (array $items, string $title): array => [
                'title' => $title,
                'items' => collect($items)
                    ->filter(fn (array $item): bool => ! $homeOnly || ($item['home'] ?? false))
                    ->map(fn (array $item): array => [
                        'question' => $item['question'],
                        'answer' => $item['answer'],
                    ])
                    ->values()
                    ->all(),
            ])
            ->filter(fn (array $group): bool => $group['items'] !== [])
            ->values()
            ->all();
    }

    public function terms(): Response
    {
        return Inertia::render('Site/Terms', [
            'seo' => [
                'title' => 'Terms of Service',
                'description' => 'The terms you agree to when you use Lua.',
            ],
        ]);
    }

    public function privacy(): Response
    {
        return Inertia::render('Site/Privacy', [
            'seo' => [
                'title' => 'Privacy Policy',
                'description' => 'What Lua collects when someone clicks a link, what it does not, and how long any of it is kept.',
            ],
        ]);
    }
}
