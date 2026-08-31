<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Use cases
|--------------------------------------------------------------------------
|
| One entry per audience, rendered by Site/UseCases/Show.vue. Same arrangement
| as config/alternatives.php: the keys are the registry, the slug is the URL,
| and adding one touches no route and no component.
|
| Shape:
|   name        the audience, as they would describe themselves
|   seo         title (no brand, the callback appends it) and description
|   intro       what they are actually trying to do, before Lua is mentioned
|   problem     the specific thing that goes wrong without a tool
|   steps       how it is done here. A real sequence, not a feature list
|   features    the parts of Lua that carry it, keyed to what exists
|   caveat      where this breaks down or is not worth it. Honest, always
|
*/

return [

    'marketing-campaigns' => [
        'name' => 'Marketing campaigns',
        'seo' => [
            'title' => 'Track marketing campaigns with short links',
            'description' => 'Tag every placement before you publish it, and read back which one actually moved. UTM parameters, per-campaign tags, and click data that does not expire.',
        ],
        'intro' => 'A campaign runs in a dozen places at once: an email, three social posts, a partner newsletter, a paid placement. They all point at the same page, so the page cannot tell them apart. The question you will be asked afterwards is which one was worth doing again.',
        'problem' => 'Referrer data will not answer it. A link opened from an app sends no referrer, most sites send only their domain, and anything typed or scanned sends nothing at all, so a large share of the traffic lands in a bucket called Direct that is not a source but the absence of one.',
        'steps' => [
            ['title' => 'Tag before you publish', 'description' => 'Put UTM parameters on each placement while you are still making the links. They travel in the URL, where no privacy setting strips them, and they survive being pasted into a chat window.'],
            ['title' => 'Group the campaign with a tag', 'description' => 'One tag across every link in the campaign, so the whole thing reads as a unit rather than as a dozen unrelated rows.'],
            ['title' => 'Read it back by placement', 'description' => 'Filter by source, medium or campaign, over the range the campaign actually ran. Compare the placements against each other rather than against a total.'],
        ],
        'features' => ['UTM parameters on every link', 'Tags to group a campaign', 'Filter by any dimension', 'Any date range, kept'],
        'caveat' => 'Lua measures the click and stops there. It cannot tell you what a placement sold, because the redirect is the last thing it sees. Joining clicks to revenue needs something running on the destination page.',
    ],

    'agencies' => [
        'name' => 'Agencies',
        'seo' => [
            'title' => 'Short links for agencies running client brands',
            'description' => 'A domain per client, links grouped by account, and analytics you can hand over. Predictable pricing that does not scale per brand.',
        ],
        'intro' => 'An agency runs links on behalf of brands that are not its own. Every link a client sees should carry the client\'s name, not the agency\'s and certainly not a shortener\'s, and the numbers have to be exportable when the client asks for them or when the relationship ends.',
        'problem' => 'Most shorteners price per workspace or per brand, so the bill grows with the client list rather than with the work. And a link on a shared domain ties the client\'s campaign to a reputation shared with everyone else using that shortener, including whoever is sending phishing through it this morning.',
        'steps' => [
            ['title' => 'A domain per client', 'description' => 'Point each client\'s own subdomain at Lua. Their links carry their name, and if they leave they keep working: the mapping moves with the domain.'],
            ['title' => 'Tag by account', 'description' => 'Group every link by client and by campaign, so a report is a filter rather than a spreadsheet exercise.'],
            ['title' => 'Hand the numbers over', 'description' => 'Everything on the screens is reachable over the REST API, so a client report can be generated rather than screenshotted.'],
        ],
        'features' => ['Custom domains, one per client', 'Tags per account and campaign', 'Team members with their own access', 'REST API for reporting'],
        'caveat' => 'Lua has one workspace per team rather than a client-switcher, so an agency separates clients by domain and tag rather than by account. That works well up to a point; a very large roster may want a workspace per client.',
    ],

    'creators' => [
        'name' => 'Creators',
        'seo' => [
            'title' => 'Short links for creators, with the analytics behind them',
            'description' => 'One link in the bio, one on screen, one in the description, and a clear answer about which of them people actually open.',
        ],
        'intro' => 'A creator publishes the same link in places that are structurally different: a bio field that allows one URL, a video where the link is spoken or shown rather than clicked, a description below it, a story that disappears in a day.',
        'problem' => 'Those placements are invisible to each other. The bio link and the description link point at the same page, so the page reports one number, and there is no way to tell whether the video worked or the profile did.',
        'steps' => [
            ['title' => 'One link per placement', 'description' => 'Make a separate short link for the bio, the description and the story. Same destination, different links, so each one keeps its own count.'],
            ['title' => 'Use the QR code on screen', 'description' => 'Every link has one, and scans are counted apart from clicks, so a code held up in a video never gets mixed in with taps from the description.'],
            ['title' => 'Compare them honestly', 'description' => 'Read the links side by side over the same range. The comparison is between your own placements, which is the only comparison that means anything.'],
        ],
        'features' => ['A QR code on every link', 'Scans counted apart from clicks', 'Separate iOS and Android destinations', 'Link expiry for anything seasonal'],
        'caveat' => 'Lua does not host a link-in-bio page. If you need one page listing several destinations, that is a different product; Lua is the measurement underneath whichever links you publish.',
    ],

    'print-and-qr' => [
        'name' => 'Print and QR codes',
        'seo' => [
            'title' => 'QR codes and short links for print that outlive the campaign',
            'description' => 'A code on packaging lasts years. Make sure the link behind it can be repointed, and that it never depended on somebody else staying in business.',
        ],
        'intro' => 'Print is the one place a link becomes permanent. A code on a poster, a label, a badge or packaging will be scanned long after the campaign that made it has ended, and it cannot be edited once it is printed.',
        'problem' => 'That permanence is exactly what makes a generic shortener dangerous. If the service closes, every printed code closes with it, and there is no recourse because the domain was never yours. A code that resolves to nothing is worse than no code.',
        'steps' => [
            ['title' => 'Print your own domain', 'description' => 'The code encodes a URL on a domain you renew. Change providers later and the printed code keeps working, because the name did not move.'],
            ['title' => 'Point it somewhere you can change', 'description' => 'The destination behind a short link is editable. Print the code once, repoint it every season.'],
            ['title' => 'Read scans separately', 'description' => 'QR scans are counted apart from clicks, so the poster and the newsletter never end up in the same number.'],
        ],
        'features' => ['A QR code on every link', 'Editable destinations', 'Scans counted separately', 'Custom domains'],
        'caveat' => 'A short domain you might let expire is worse than a long one you will keep. An expired short domain does not fail politely: somebody buys it and your printed codes point at their page.',
    ],

    'developers' => [
        'name' => 'Developers',
        'seo' => [
            'title' => 'A link shortener with a real API, an MCP server, and self-hosting',
            'description' => 'Every action the screens do is reachable over REST, an MCP server lets an agent drive it, and you can run the whole thing on your own server.',
        ],
        'intro' => 'Sometimes the shortener is a component rather than a product: links created by a job, read by a dashboard, or driven by an assistant, with nobody opening the screens at all.',
        'problem' => 'Most shorteners treat the API as a tier rather than as the product, so the interesting operations are dashboard-only or metered separately, and self-hosting is not offered at any price.',
        'steps' => [
            ['title' => 'Call the REST API', 'description' => 'Create, update, tag and read links, and read the analytics. It reaches every action the screens do, because both call the same code underneath.'],
            ['title' => 'Or hand it to an agent', 'description' => 'An MCP server ships with it, so an assistant can create a link, tag it and read its analytics with no scraping layer in between.'],
            ['title' => 'Run it yourself if it matters', 'description' => 'It is open source and runs on PostgreSQL or MySQL. Self-hosting is the only way to guarantee the click record never reaches anyone else\'s infrastructure.'],
        ],
        'features' => ['REST API covering every action', 'MCP server for AI agents', 'API tokens', 'Self-hostable, open source'],
        'caveat' => 'Self-hosting is infrastructure you operate: upgrades, backups, and somebody who can read logs when redirects stop. It is a good trade for some teams and a slow distraction for others.',
    ],

    'newsletters' => [
        'name' => 'Newsletters',
        'seo' => [
            'title' => 'Short links for newsletters that do not trip spam filters',
            'description' => 'A generic shortener in an email is a category that filters judge before they read it. Your own domain carries your own reputation.',
        ],
        'intro' => 'A newsletter is mostly links, and every one of them is a chance for a mail gateway to decide the message is not worth delivering.',
        'problem' => 'Generic shortener domains are heavily used for phishing, so they are treated as a class rather than judged one link at a time. Gateways rewrite or strip them, some filters weigh them, and none of that is a judgement about your specific link.',
        'steps' => [
            ['title' => 'Send from a domain you control', 'description' => 'A subdomain of the domain the newsletter already comes from keeps the link consistent with the sender, and carries your reputation rather than a stranger\'s.'],
            ['title' => 'Tag the issue', 'description' => 'One tag per issue and UTM parameters per link, so a click reads back as "issue 42, the second link" rather than as an anonymous visit.'],
            ['title' => 'Compare issues, not totals', 'description' => 'Filter by campaign over the week each issue went out. What matters is whether this issue beat the last one.'],
        ],
        'features' => ['Custom domains', 'UTM parameters', 'Tags per issue', 'Click history that does not expire'],
        'caveat' => 'A short link cannot tell you who clicked. Email platforms can, because they identify the recipient; Lua sets no cookie and sees only the request.',
    ],

];
