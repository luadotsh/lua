<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Glossary
|--------------------------------------------------------------------------
|
| One entry per term, rendered by Site/Glossary/Show.vue. Keys are the
| registry and the slug is the URL, same as alternatives and use cases.
|
| Shape:
|   term      how the term is written in prose
|   short     one sentence. This is the definition, and it is what the index
|             and the structured data use, so it has to stand alone
|   body      the paragraphs that follow. Where it comes from, what people get
|             wrong about it, what it is worth in practice
|   related   slugs of other entries. A term nobody can leave is a dead end
|
| Every entry has to be true of how the web actually behaves, not of how a
| marketing page wishes it did.
|
*/

return [

    'utm-parameters' => [
        'term' => 'UTM parameters',
        'short' => 'Tags added to a URL\'s query string that name where a link was published, so analytics can tell one placement from another.',
        'body' => [
            'There are five, and they are a naming convention rather than a standard: utm_source names where the link was published, utm_medium names how it travelled, utm_campaign groups the placements together, and utm_term and utm_content are optional detail. Analytics tools read them because everyone agreed to, not because a specification says they must.',
            'They exist because the alternative does not work. The referrer header is missing on most clicks, so a tool relying on it reports a large share of traffic as coming from nowhere. UTM parameters travel in the URL itself, where no privacy setting strips them and where they survive being pasted into a chat window.',
            'The common mistakes are inconsistency and over-tagging. "Newsletter", "newsletter" and "Email newsletter" are three sources to a tool that compares strings, and a tag left empty is worse than absent because it reports a blank placement. Pick a convention and apply it before publishing, because a link cannot be retagged after it is out in the world.',
        ],
        'related' => ['referrer', 'attribution', 'query-string'],
    ],

    'referrer' => [
        'term' => 'Referrer',
        'short' => 'The page a visitor came from, as reported by their browser in the Referer header, when it reports one at all.',
        'body' => [
            'The header is famously misspelled in the HTTP specification, which is why code says Referer and prose says referrer. It is sent by the browser rather than by the site, which means the site being linked to has no control over whether it arrives.',
            'It is missing far more often than people expect. A link opened inside a native app usually sends nothing. Most sites now send a Referrer-Policy of strict-origin-when-cross-origin, which gives the domain and never the path. Anything typed, pasted or scanned from a QR code has no referring page by definition.',
            'This is why analytics screens show a large bucket called Direct. Direct is not a traffic source. It is the absence of one, and reading it as "people who came straight to us" is the single most common misreading of a link dashboard.',
        ],
        'related' => ['utm-parameters', 'referrer-policy', 'attribution'],
    ],

    'referrer-policy' => [
        'term' => 'Referrer-Policy',
        'short' => 'A header a site sends to control how much of its URL is passed on when someone follows a link away from it.',
        'body' => [
            'The policy ranges from no-referrer, which sends nothing, through origin-when-cross-origin, which sends only the domain, to unsafe-url, which sends the full path to anyone. Browsers now default to strict-origin-when-cross-origin when a site sets nothing, which is why referrer data got noticeably less useful without anything changing on the receiving end.',
            'The reason is privacy rather than analytics. A full referrer leaks the path of the page somebody was reading, which on a search page or a private document is information the destination has no business receiving.',
            'For anyone measuring links, the practical consequence is that the referrer will tell you the domain at best, and often nothing. Tag the link instead.',
        ],
        'related' => ['referrer', 'utm-parameters'],
    ],

    'redirect' => [
        'term' => 'Redirect',
        'short' => 'An HTTP response that tells the browser to go somewhere else, using a 3xx status and a Location header.',
        'body' => [
            'A short link is a redirect and nothing more: the server looks up where the key points, answers with a status and a Location, and the browser follows it. Everything a shortener knows, it learns in that one request.',
            'The status matters. 301 says the move is permanent and browsers may cache it, which means a repeat visitor might never ask again and the click never gets counted. 302 and 307 say temporary, so the browser asks every time. A shortener that wants to count clicks and allow the destination to change uses a temporary status for exactly this reason.',
            'Chains are legal and common: one redirect leading to another. Each hop costs a round trip, and each is a place the chain can break.',
        ],
        'related' => ['301-vs-302', 'link-rot', 'short-link'],
    ],

    '301-vs-302' => [
        'term' => '301 vs 302',
        'short' => 'Permanent and temporary redirects. The difference decides whether browsers cache the hop, which decides whether you can count it or change it later.',
        'body' => [
            'A 301 tells the browser the resource has moved for good. Browsers are allowed to remember that and skip asking next time, and they do. It is the right answer when a page genuinely moved and you want search engines to transfer the ranking.',
            'A 302 (or 307, which is the stricter modern equivalent) says the redirect is temporary. The browser asks the server every time, which is what makes a click countable and what lets you repoint the link later.',
            'For short links the choice is not close. A 301 on a marketing link means a repeat visitor never hits your server again: they are not counted, and if you change the destination they still go to the old one, sometimes for months.',
        ],
        'related' => ['redirect', 'short-link'],
    ],

    'short-link' => [
        'term' => 'Short link',
        'short' => 'A brief URL that redirects to a longer one, used to make links shareable, printable, and countable.',
        'body' => [
            'The structure is a domain and a back-half: example.com/spring. The domain is the part a reader recognises, and the back-half is the key the server looks up.',
            'Shortening solves three separate problems that get conflated. It fits a URL into a place with a character limit or no room for a query string. It makes a URL something a person can read aloud or type off a poster. And it puts a redirect in the middle, which is the only reason the click can be measured at all.',
            'The tradeoff is that a short link hides its destination, which is exactly why phishing uses them and why the domain a link wears carries so much weight.',
        ],
        'related' => ['back-half', 'custom-domain', 'redirect'],
    ],

    'back-half' => [
        'term' => 'Back-half',
        'short' => 'The part of a short link after the domain: the key the server looks up to find the destination.',
        'body' => [
            'It can be generated, which produces something like /aB3x9, or chosen, which produces something like /spring. A chosen back-half is worth the effort on anything a person will read, because /spring survives being retyped from a poster and /aB3x9 does not.',
            'Case usually matters. On most shorteners /Spring and /spring are two different links, because the lookup compares the stored key exactly.',
            'Some back-halves cannot be used on a shortener\'s main domain, because a real page already answers at that path. On your own domain, nothing is reserved.',
        ],
        'related' => ['short-link', 'custom-domain'],
    ],

    'custom-domain' => [
        'term' => 'Custom domain',
        'short' => 'A domain you own, pointed at a link shortener, so your short links carry your name instead of the shortener\'s.',
        'body' => [
            'Setting one up is a CNAME record and a certificate, which usually takes minutes. A subdomain of a domain you already own, such as go.example.com, works exactly as well as a short new domain and costs nothing extra.',
            'It matters more than it sounds. A short link hides its destination, so the domain is the only signal the reader gets about who is asking. It is also what keeps generic shortener domains, which are heavily used for phishing, from getting your links filtered as a category rather than judged one at a time.',
            'The part people notice last is ownership. If the shortener closes, links on its domain close with it. Links on your domain are a mapping you hold and a name you renew, so you can point them somewhere else and everything already published keeps working.',
        ],
        'related' => ['cname-record', 'link-rot', 'short-link'],
    ],

    'cname-record' => [
        'term' => 'CNAME record',
        'short' => 'A DNS record that points one hostname at another, which is how a custom domain is attached to a shortener.',
        'body' => [
            'It says "go.example.com is an alias for whatever this other name resolves to", so the shortener can change its own infrastructure without every customer editing DNS.',
            'A CNAME cannot sit at the root of a domain in standard DNS, which is why shorteners ask for a subdomain. Some providers offer a flattening record that works around it.',
            'Propagation is usually minutes rather than the 48 hours folklore suggests, and it is bounded by the TTL of the record you replaced.',
        ],
        'related' => ['custom-domain'],
    ],

    'qr-code' => [
        'term' => 'QR code',
        'short' => 'A two-dimensional barcode that encodes text, usually a URL, so a camera can open it without anyone typing.',
        'body' => [
            'It carries error correction, in four levels: at the highest, roughly a third of the code can be damaged or covered and it still reads. That is what allows a logo in the middle, and it is why a printed code survives a scuffed label.',
            'The code encodes whatever it was given, permanently. Encode a destination directly and the printed artwork is the last decision you get to make. Encode a short link and the destination stays editable for as long as you hold the domain.',
            'For anything printed, take the SVG. It stays sharp at poster size, and a PNG scaled up does not.',
        ],
        'related' => ['short-link', 'custom-domain'],
    ],

    'link-rot' => [
        'term' => 'Link rot',
        'short' => 'The steady failure of published links over time, as destinations move, sites close, or the service in the middle shuts down.',
        'body' => [
            'It is not hypothetical for short links specifically. Shorteners have closed and taken every link with them, and the people who had printed those links had no recourse, because the domain was never theirs.',
            'The exposure is worst wherever a link becomes permanent: a flyer, a conference badge, a product label, a book. Those cannot be edited, and they will be scanned long after the campaign that made them.',
            'The mitigation is ownership rather than choosing a bigger vendor. A link on a domain you renew survives a change of provider, because the mapping is yours and the name did not move.',
        ],
        'related' => ['custom-domain', 'short-link'],
    ],

    'open-redirect' => [
        'term' => 'Open redirect',
        'short' => 'A URL on a trusted domain that will forward a visitor to any destination given to it, which attackers use to borrow that domain\'s reputation.',
        'body' => [
            'The pattern is a parameter like ?next= or ?url= that the server follows without checking. Because the visible domain is trusted, a filter or a person may wave the link through and land somewhere else entirely.',
            'A link shortener is a redirect by design, which is why the domain a short link wears matters so much, and why generic shortener domains are treated with suspicion as a class.',
            'The defence on your own service is to only ever redirect to destinations you stored yourself, never to one supplied in the request.',
        ],
        'related' => ['redirect', 'custom-domain'],
    ],

    'geolocation' => [
        'term' => 'IP geolocation',
        'short' => 'Estimating where a visitor is by looking their IP address up in a database of allocated ranges.',
        'body' => [
            'The three levels are not equally reliable, and treating them as if they were is the most common way to misread a dashboard. Country is dependable, because registries allocate address blocks by country. Region is usually right. City is a guess.',
            'City is wrong in a specific direction: it tends to report where the network terminates rather than where the person is. Mobile traffic shows this most clearly, because a phone in a small town often resolves to whichever city the carrier routes through, so a city breakdown for a mobile-heavy audience quietly over-counts a few large ones.',
            'VPNs, satellite connections and corporate proxies all resolve somewhere other than the visitor.',
        ],
        'related' => ['user-agent', 'attribution'],
    ],

    'user-agent' => [
        'term' => 'User agent',
        'short' => 'A string a browser sends identifying itself, its version and the operating system, from which device and browser breakdowns are derived.',
        'body' => [
            'It is parsed by pattern matching, which has an ordering trap that catches most implementations. Edge, Opera, Brave, Vivaldi and Samsung Internet all contain the word Chrome, because they are all Chromium, so a parser that checks for Chrome first reports every one of them as Chrome. A dashboard with a suspiciously round Chrome share and no Edge at all is showing you that bug.',
            'The string is also getting deliberately less informative. Browsers have been freezing and trimming what they report for years, moving the detail behind User-Agent Client Hints, which a server has to ask for.',
            'Bots and link preview fetchers send user agents too. The well-behaved ones identify themselves and can be filtered; the rest cannot.',
        ],
        'related' => ['bot-traffic', 'geolocation'],
    ],

    'bot-traffic' => [
        'term' => 'Bot traffic',
        'short' => 'Automated requests to a link that no person made: previews, security scanners, crawlers and monitoring.',
        'body' => [
            'Post a link in a chat app and it gets fetched before anyone reads the message, so the preview can be built. Send it by email and a security gateway may open it to check where it goes. Both look like clicks.',
            'This is why a link shared into a large group can show clicks arriving before the message does, and why a link in a newsletter can show a click count that outruns the opens.',
            'Well-behaved automation identifies itself in the user agent and can be filtered out. The rest cannot, which means every click number has some unknown amount of machine in it.',
        ],
        'related' => ['user-agent', 'unique-clicks'],
    ],

    'unique-clicks' => [
        'term' => 'Unique clicks',
        'short' => 'An estimate of how many separate people opened a link, as distinct from how many times it was opened.',
        'body' => [
            'It is an estimate, and it is worth knowing how weak one. Without a cookie there is no identity in a redirect, so the count is derived from what the request carried: the address, the user agent, the time. Two people behind the same office network can collapse into one, and one person on a train can become several as their address changes.',
            'A shortener that sets no cookie at the redirect cannot follow the same person from one of your links to another either, so a visitor number is per link rather than per person.',
            'Read it as a scale rather than a headcount. The trend between two weeks is meaningful; the absolute number is not.',
        ],
        'related' => ['bot-traffic', 'attribution'],
    ],

    'attribution' => [
        'term' => 'Attribution',
        'short' => 'Deciding which placement, campaign or channel deserves credit for an outcome.',
        'body' => [
            'A short link can attribute a click, which is a narrow and honest claim: this placement produced this many opens, from these countries, on these devices. That is the whole of what a redirect can see.',
            'It cannot attribute a sale. The redirect is the last thing the shortener observes, and what happened on the destination page belongs to the destination. Joining the two requires something running there, tying the visit to the eventual purchase.',
            'Any tool claiming otherwise from the link alone is either running a script on your site or describing something it does not do.',
        ],
        'related' => ['utm-parameters', 'unique-clicks', 'referrer'],
    ],

    'query-string' => [
        'term' => 'Query string',
        'short' => 'The part of a URL after the question mark, carrying key and value pairs that travel with the request.',
        'body' => [
            'It is where UTM parameters live, which is why they survive when the referrer does not: the query string is part of the URL and goes wherever the URL goes, including into a chat message or a printed QR code.',
            'It is also visible to everyone in the chain, so it is the wrong place for anything private. An identifier in a query string ends up in server logs, in browser history and in the referrer sent onwards.',
            'Order does not matter to a server reading parameters, but it does to anything comparing URLs as strings, including some caches and most analytics tools.',
        ],
        'related' => ['utm-parameters', 'short-link'],
    ],

];
