<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Frequently asked questions
|--------------------------------------------------------------------------
|
| One source for the /faq page, the short set on the home page, and the
| FAQPage structured data both emit. Adding a question here adds it to all
| three; there is no second list to keep in step.
|
| Every answer has to be true of the product as it stands. `home` marks the
| handful that lead on the home page, and they are the ones a stranger asks
| before signing up rather than the ones a customer asks afterwards.
|
*/

return [

    'Getting started' => [
        [
            'question' => 'What does Lua actually do?',
            'answer' => 'It turns a long URL into a short one on a domain you control, then records what happens when someone opens it: which country, which device, which browser, which referrer, and any UTM parameters on the link. The short link is the mechanism. The record is the product.',
            'home' => true,
        ],
        [
            'question' => 'Do I need a domain of my own?',
            'answer' => 'No. Links work on the shared domain from the moment you sign up. A domain of your own is worth adding when you start publishing links somewhere permanent, because a branded link gets clicked more often and keeps working if you ever change providers.',
            'home' => true,
        ],
        [
            'question' => 'How long does a custom domain take to set up?',
            'answer' => 'Point a CNAME at Lua and wait for the certificate, which is usually minutes. A subdomain of a domain you already own, like go.example.com, works perfectly and costs nothing extra.',
        ],
    ],

    'Links' => [
        [
            'question' => 'Can I choose the back-half myself?',
            'answer' => 'Yes. Letters, numbers, hyphens and underscores. Case matters, so /Spring and /spring are two different links. A handful of words are reserved on the main domain because a real page already answers there; on your own domain nothing is reserved.',
        ],
        [
            'question' => 'Can a link expire, or ask for a password?',
            'answer' => 'Both, on every plan including the free one. A link can retire on a date and send people to a fallback URL after it, and it can sit behind a password. A link can also send iOS and Android to different destinations.',
        ],
        [
            'question' => 'Does every link get a QR code?',
            'answer' => 'Yes, and scans are counted separately from clicks, so print and screen never end up in the same number.',
        ],
        [
            'question' => 'What happens to my links if I stop paying?',
            'answer' => 'They keep resolving while the account is open. Closing the account stops them, which is why a domain of your own matters for anything printed: the mapping is yours, and you can point it elsewhere.',
        ],
    ],

    'Analytics' => [
        [
            'question' => 'Is the analytics data limited on cheaper plans?',
            'answer' => 'No. Every field and every date range is on every plan, the free one included. Plans differ on five numbers only: links, click events a month, custom domains, tags and team members.',
            'home' => true,
        ],
        [
            'question' => 'How far back can I look?',
            'answer' => 'As far back as the link goes. Click history is kept rather than rolled off after a window, so a campaign from last year is still readable.',
        ],
        [
            'question' => 'How accurate is the location?',
            'answer' => 'Country is dependable, because address blocks are allocated by country. Region is usually right. City is a guess that tends to report where the network terminates rather than where the person is, which shows up most on mobile traffic. Read country as fact and city as a hint.',
        ],
        [
            'question' => 'Why do so many clicks say "Direct"?',
            'answer' => 'Because browsers increasingly send no referrer. A link opened inside an app, typed, pasted, or scanned from a QR code has no referring page at all, and most sites now send only their domain rather than the page. Direct is the absence of a source, not a source. Tag links with UTM parameters when the answer matters: those travel in the URL, where nothing strips them.',
            'home' => true,
        ],
        [
            'question' => 'Does Lua track people across links?',
            'answer' => 'No. No cookie is set at the redirect, so the same person opening two of your links is two unrelated rows. Visitor counts are an estimate built from what the request carried.',
        ],
    ],

    'Plans and billing' => [
        [
            'question' => 'What is actually in the free plan?',
            'answer' => 'Five links, 100 click events a month, one tag and one team member, on the shared domain. Every feature and every analytics field is the same as the paid plans.',
        ],
        [
            'question' => 'Is yearly cheaper?',
            'answer' => 'Yes, two months free. The pricing page shows both, always as a monthly figure so the two are comparable.',
        ],
        [
            'question' => 'What happens when I hit a limit?',
            'answer' => 'New work of that kind stops. Nothing you already have is deleted, and existing links keep resolving.',
        ],
    ],

    'Self-hosting and developers' => [
        [
            'question' => 'Can I run Lua on my own server?',
            'answer' => 'Yes. It is open source, and self-hosting is the only way to guarantee the click record never reaches anyone else\'s infrastructure. It runs on PostgreSQL or MySQL.',
            'home' => true,
        ],
        [
            'question' => 'Is there an API?',
            'answer' => 'Yes, and it reaches every action the screens do, because both call the same code. There is also an MCP server, so an assistant can create a link, tag it and read its analytics without a scraping layer in between.',
        ],
        [
            'question' => 'What does Lua store about the people who click?',
            'answer' => 'The click event holds the derived location, the device, browser, OS and language, the referrer, any UTM parameters, and the IP address the location was derived from. The IP is never shown in the analytics screens and is deleted with the link. Self-hosted, it never leaves your own server.',
        ],
    ],

];
