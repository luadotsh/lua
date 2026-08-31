<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Competitor comparisons
|--------------------------------------------------------------------------
|
| One entry per rival, rendered by Site/Alternatives/Show.vue. The template
| reads whatever it finds here, so adding a comparison is a new key in this
| file and nothing else: no component, no route, no registry to keep in step.
| The keys themselves are the registry, and the slug is the URL.
|
| Every entry has the same shape:
|
|   name           the competitor, as people write it. Used in the heading,
|                  both table columns and the CTA.
|   seo            title and description for the page.
|   intro          the opening paragraph. Say what they are good at first —
|                  a comparison nobody believes persuades nobody.
|   reasons        why someone moves. Title plus a sentence.
|   comparison     feature, ours, theirs. One row per thing worth comparing.
|   pricing        the same, for money.
|   fit.good       who should switch.
|   fit.bad        who should not. Leaving this honest is what makes the
|                  rest of the page worth reading.
|
*/

return [

    'bitly' => [
        'name' => 'Bitly',

        'seo' => [
            'title' => 'Bitly alternative with analytics on every plan',
            'description' => 'A Bitly alternative that does not meter clicks or lock analytics behind a tier. Custom domains, full click history, an API and MCP, self-hostable.',
        ],

        'intro' => 'Bitly invented this category and still runs most of it: the links are fast, the brand is trusted, and a short link that says bit.ly needs no explanation. What it charges for is the part that comes after the click. The free plan caps how many links you can make each month and gives you no custom domain, analytics stop at a rolling window, and the click data older than that window is gone rather than archived. Lua is the same job with the reporting left switched on, and you can run it on your own server if you would rather the click data never left it.',

        'reasons' => [
            [
                'title' => 'The click history does not expire',
                'description' => 'Bitly keeps detailed analytics for a window that depends on your plan, and a campaign you want to look back on next year is often past it. Lua keeps the events and lets you pick any range.',
            ],
            [
                'title' => 'A custom domain is not the upgrade',
                'description' => 'A branded short link is the whole point of a short link, and on Bitly it is the thing behind the paywall. Lua treats a domain as configuration, not a tier.',
            ],
            [
                'title' => 'You can run it yourself',
                'description' => 'Lua is open source and self-hostable, so the record of who clicked what can stay on infrastructure you control. That is not a thing a hosted-only product can offer.',
            ],
            [
                'title' => 'An API and an MCP server, not an add-on',
                'description' => 'Every action in Lua goes through the same code whether it comes from the screen, the REST API or an AI agent over MCP. Bitly gates API volume by plan.',
            ],
        ],

        'comparison' => [
            ['feature' => 'Custom domain', 'lua' => 'Included, unlimited on paid', 'competitor' => 'Paid plans only'],
            ['feature' => 'Click history', 'lua' => 'Kept, any date range', 'competitor' => 'Rolling window by plan'],
            ['feature' => 'Analytics detail', 'lua' => 'Country, region, city, device, browser, OS, referrer, UTM', 'competitor' => 'Depends on plan'],
            ['feature' => 'QR codes', 'lua' => 'Included, scans counted separately', 'competitor' => 'Included, styling by plan'],
            ['feature' => 'REST API', 'lua' => 'Included', 'competitor' => 'Volume by plan'],
            ['feature' => 'MCP server for AI agents', 'lua' => 'Included', 'competitor' => 'Not offered'],
            ['feature' => 'Self-hosting', 'lua' => 'Open source, run it anywhere', 'competitor' => 'Hosted only'],
            ['feature' => 'Link expiry and password', 'lua' => 'Included', 'competitor' => 'Higher plans'],
        ],

        'pricing' => [
            ['tier' => 'Getting started', 'lua' => 'Free, 5 links', 'competitor' => 'Free, limited links, no custom domain'],
            ['tier' => 'A brand and its domain', 'lua' => 'Paid plan, domains included', 'competitor' => 'Paid tier for the domain'],
            ['tier' => 'Keeping the data yourself', 'lua' => 'Self-host at cost', 'competitor' => 'Not offered'],
        ],

        'fit' => [
            'good' => [
                'title' => 'Switch if you',
                'items' => [
                    'Want to look at a campaign from last year, not just last month',
                    'Need a branded domain without it being the reason you upgrade',
                    'Would rather the click data sat on your own infrastructure',
                    'Want an AI agent to create and read links through MCP',
                ],
            ],
            'bad' => [
                'title' => 'Stay on Bitly if you',
                'items' => [
                    'Need the recognition of a bit.ly link in the wild',
                    'Rely on their integrations directory or an enterprise agreement',
                    'Want link-in-bio pages and campaign management in the same product',
                    'Have no appetite to move links that are already printed somewhere',
                ],
            ],
        ],
    ],

    'dub' => [
        'name' => 'Dub',

        'seo' => [
            'title' => 'Lua vs Dub: two open source link platforms compared',
            'description' => 'Dub is a genuinely good open source link platform. Here is where the two actually differ, and why the honest answer is that you may not need to switch.',
        ],

        'intro' => 'This is the comparison where the honest answer is least convenient for us. Dub is open source, self-hostable, developer-first, and well built. Most of the arguments Lua makes against a closed shortener do not land here, because Dub already agrees with them. What is left is a narrower question about scope and how much product you want around the link, and that is the question this page tries to answer rather than dodge.',

        'reasons' => [
            [
                'title' => 'A smaller surface to run',
                'description' => 'Dub has grown well beyond links into a broader platform. If you want a shortener and its analytics and nothing else, Lua is less to deploy, less to configure and less to keep upgraded.',
            ],
            [
                'title' => 'MySQL as well as PostgreSQL',
                'description' => 'Lua runs on either, which matters if you self-host onto infrastructure you already have rather than choosing a database for one service.',
            ],
            [
                'title' => 'A Laravel codebase',
                'description' => 'If your team writes PHP, extending Lua is a Tuesday afternoon rather than a new stack to learn. If your team writes TypeScript, that argument runs the other way and you should weigh it accordingly.',
            ],
        ],

        'comparison' => [
            ['feature' => 'Open source', 'lua' => 'Yes', 'competitor' => 'Yes'],
            ['feature' => 'Self-hosting', 'lua' => 'Yes', 'competitor' => 'Yes'],
            ['feature' => 'Custom domains', 'lua' => 'Included', 'competitor' => 'Included'],
            ['feature' => 'Click analytics', 'lua' => 'Country, region, city, device, browser, OS, referrer, UTM', 'competitor' => 'Comparable, plus more'],
            ['feature' => 'REST API', 'lua' => 'Included', 'competitor' => 'Included'],
            ['feature' => 'MCP server', 'lua' => 'Included', 'competitor' => 'Available'],
            ['feature' => 'Stack', 'lua' => 'Laravel and Vue, PostgreSQL or MySQL', 'competitor' => 'Next.js and TypeScript'],
            ['feature' => 'Scope', 'lua' => 'Links and their analytics', 'competitor' => 'A broader link platform'],
        ],

        'pricing' => [
            ['tier' => 'Getting started', 'lua' => 'Free tier', 'competitor' => 'Free tier'],
            ['tier' => 'Self-hosting', 'lua' => 'Free, your server', 'competitor' => 'Free, your server'],
            ['tier' => 'Hosted', 'lua' => 'Paid plans', 'competitor' => 'Paid plans'],
        ],

        'fit' => [
            'good' => [
                'title' => 'Switch if you',
                'items' => [
                    'Write PHP and want to extend the thing you run',
                    'Want to self-host onto MySQL rather than adopt PostgreSQL for one service',
                    'Want a shortener and its analytics, not a platform around them',
                ],
            ],
            'bad' => [
                'title' => 'Stay on Dub if you',
                'items' => [
                    'Already run it and it works, because the gap does not justify a migration',
                    'Write TypeScript and want to modify the codebase',
                    'Use the parts of it that reach past links',
                    'Want the larger ecosystem and integration surface',
                ],
            ],
        ],
    ],

    'short-io' => [
        'name' => 'Short.io',

        'seo' => [
            'title' => 'Short.io alternative that is open source and self-hostable',
            'description' => 'Short.io is built around custom domains and does that well. Lua is the same job with the source open and the option to run it yourself.',
        ],

        'intro' => 'Short.io took the right lesson early: the branded domain is the product, not the upgrade. Its free tier has long been unusually generous about domains, which is the opposite of how most shorteners are priced, and it deserves credit for that. Where the two part company is ownership. Short.io is a hosted service, so the click record lives on their infrastructure and the roadmap is theirs.',

        'reasons' => [
            [
                'title' => 'You can run it yourself',
                'description' => 'Lua is open source, so the record of who clicked what can stay on infrastructure you control. That is not something a hosted-only product can offer at any tier.',
            ],
            [
                'title' => 'The pricing is not usage-metered per click',
                'description' => 'Lua meters five numbers and none of them is a per-click charge. Read both pricing pages against your actual volume, because this is where the two diverge most.',
            ],
            [
                'title' => 'An MCP server, not just an API',
                'description' => 'An assistant can create a link, tag it and read its analytics directly, without a scraping layer or a custom integration in between.',
            ],
        ],

        'comparison' => [
            ['feature' => 'Custom domains', 'lua' => 'Included', 'competitor' => 'Included, generous free tier'],
            ['feature' => 'Open source', 'lua' => 'Yes', 'competitor' => 'No'],
            ['feature' => 'Self-hosting', 'lua' => 'Yes', 'competitor' => 'Not offered'],
            ['feature' => 'Click analytics', 'lua' => 'Country, region, city, device, browser, OS, referrer, UTM', 'competitor' => 'Comparable'],
            ['feature' => 'REST API', 'lua' => 'Included', 'competitor' => 'Included'],
            ['feature' => 'MCP server', 'lua' => 'Included', 'competitor' => 'Not offered'],
            ['feature' => 'Link expiry and password', 'lua' => 'Included on every plan', 'competitor' => 'Available'],
        ],

        'pricing' => [
            ['tier' => 'Getting started', 'lua' => 'Free, 5 links', 'competitor' => 'Free, domain-friendly'],
            ['tier' => 'Growing volume', 'lua' => 'Paid plans by volume', 'competitor' => 'Paid plans by volume'],
            ['tier' => 'Keeping the data yourself', 'lua' => 'Self-host at cost', 'competitor' => 'Not offered'],
        ],

        'fit' => [
            'good' => [
                'title' => 'Switch if you',
                'items' => [
                    'Want the click record on your own infrastructure',
                    'Want to read the source of the thing resolving your links',
                    'Want an assistant to drive it over MCP',
                ],
            ],
            'bad' => [
                'title' => 'Stay on Short.io if you',
                'items' => [
                    'Are inside their free domain allowance and happy there',
                    'Rely on an integration they have and we do not',
                    'Have no interest in ever running infrastructure',
                ],
            ],
        ],
    ],

    'rebrandly' => [
        'name' => 'Rebrandly',

        'seo' => [
            'title' => 'Rebrandly alternative for teams who want to own the data',
            'description' => 'Rebrandly is built for branded links at organisational scale. Lua is the same core with the source open, and without the seat maths.',
        ],

        'intro' => 'Rebrandly is aimed at organisations managing branded links at scale, with the workspace and permission machinery that implies. If you need approval flows across a large marketing department, that machinery is the product and it is worth paying for. Most teams are smaller than that, and end up paying for the org chart rather than for the links.',

        'reasons' => [
            [
                'title' => 'Pricing that does not turn on seats',
                'description' => 'Lua meters links, click events, domains, tags and team members, and nothing else. There is no per-seat multiplier on the parts you actually use.',
            ],
            [
                'title' => 'Analytics are not the tier',
                'description' => 'Every field and every date range is on every plan here, the free one included. What you pay for is volume.',
            ],
            [
                'title' => 'Open source, and self-hostable',
                'description' => 'For an agency or a regulated brand, being able to keep the click record inside your own perimeter is sometimes the whole requirement.',
            ],
        ],

        'comparison' => [
            ['feature' => 'Custom domains', 'lua' => 'Included', 'competitor' => 'Included'],
            ['feature' => 'Open source', 'lua' => 'Yes', 'competitor' => 'No'],
            ['feature' => 'Self-hosting', 'lua' => 'Yes', 'competitor' => 'Not offered'],
            ['feature' => 'Analytics detail', 'lua' => 'Full on every plan', 'competitor' => 'Varies by plan'],
            ['feature' => 'Team management', 'lua' => 'Workspace members', 'competitor' => 'Roles and permissions at depth'],
            ['feature' => 'REST API', 'lua' => 'Included', 'competitor' => 'Included'],
            ['feature' => 'MCP server', 'lua' => 'Included', 'competitor' => 'Not offered'],
        ],

        'pricing' => [
            ['tier' => 'A small team', 'lua' => 'Paid plan, members included', 'competitor' => 'Priced per seat and volume'],
            ['tier' => 'Full analytics', 'lua' => 'Every plan', 'competitor' => 'Higher tiers'],
            ['tier' => 'Keeping the data yourself', 'lua' => 'Self-host at cost', 'competitor' => 'Not offered'],
        ],

        'fit' => [
            'good' => [
                'title' => 'Switch if you',
                'items' => [
                    'Are a small team paying for enterprise machinery you do not use',
                    'Need the click data inside your own perimeter',
                    'Want full analytics without it being the upgrade',
                ],
            ],
            'bad' => [
                'title' => 'Stay on Rebrandly if you',
                'items' => [
                    'Genuinely need granular roles and approval flows',
                    'Have an enterprise agreement or procurement already in place',
                    'Depend on their integration catalogue',
                ],
            ],
        ],
    ],

    'tinyurl' => [
        'name' => 'TinyURL',

        'seo' => [
            'title' => 'TinyURL alternative with real analytics behind the link',
            'description' => 'TinyURL is the fastest way to shorten a URL and always has been. If you ever need to know what happened after the click, that is where it stops.',
        ],

        'intro' => 'TinyURL has outlasted almost everything in this category by doing one thing and not asking anything of you. Paste, shorten, done, no account. For a link you are about to send to one person, nothing beats it, and this page is not going to pretend otherwise. The comparison only matters once the link is something you publish rather than something you send.',

        'reasons' => [
            [
                'title' => 'You find out what happened',
                'description' => 'Country, region, city, device, browser, OS, referrer and UTM parameters, over any range. A link you publish and cannot measure is a link you will argue about later.',
            ],
            [
                'title' => 'The domain is yours',
                'description' => 'A branded domain is what gets a link clicked, and what keeps it working if you ever move. It is also what stops a mail gateway judging your link by the company it keeps.',
            ],
            [
                'title' => 'The link is editable',
                'description' => 'Point a printed QR code at a link whose destination you can change, and the poster stops being the last decision you get to make.',
            ],
        ],

        'comparison' => [
            ['feature' => 'Shorten without an account', 'lua' => 'Account needed', 'competitor' => 'No account needed'],
            ['feature' => 'Click analytics', 'lua' => 'Full breakdown, any range', 'competitor' => 'Minimal'],
            ['feature' => 'Custom domain', 'lua' => 'Included', 'competitor' => 'Paid plans'],
            ['feature' => 'Editable destination', 'lua' => 'Yes', 'competitor' => 'Paid plans'],
            ['feature' => 'QR codes', 'lua' => 'Included, scans counted separately', 'competitor' => 'Available'],
            ['feature' => 'REST API', 'lua' => 'Included', 'competitor' => 'Paid plans'],
            ['feature' => 'Self-hosting', 'lua' => 'Open source, run it anywhere', 'competitor' => 'Hosted only'],
        ],

        'pricing' => [
            ['tier' => 'One link, one person', 'lua' => 'Overkill', 'competitor' => 'Free, and the right tool'],
            ['tier' => 'Links you publish', 'lua' => 'Free tier, full analytics', 'competitor' => 'Paid for the useful parts'],
            ['tier' => 'Keeping the data yourself', 'lua' => 'Self-host at cost', 'competitor' => 'Not offered'],
        ],

        'fit' => [
            'good' => [
                'title' => 'Switch if you',
                'items' => [
                    'Publish links rather than send them',
                    'Need to answer which placement worked',
                    'Want the link on your own name',
                    'Are printing something you cannot reprint',
                ],
            ],
            'bad' => [
                'title' => 'Stay on TinyURL if you',
                'items' => [
                    'Want to shorten one link in five seconds with no account',
                    'Do not care what happens after the click, which is a legitimate position',
                    'Already have thousands of tinyurl.com links in the wild',
                ],
            ],
        ],
    ],

];
