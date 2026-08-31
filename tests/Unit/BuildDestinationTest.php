<?php

declare(strict_types=1);

use App\Actions\Link\BuildDestination;
use App\Models\Link;

it('appends the link utms to a destination with no query string', function () {
    $link = new Link(['utm_source' => 'newsletter', 'utm_medium' => 'email']);

    expect(BuildDestination::execute('https://example.com/pricing', $link))
        ->toBe('https://example.com/pricing?utm_source=newsletter&utm_medium=email');
});

it('keeps the query the destination already had', function () {
    $link = new Link(['utm_source' => 'newsletter']);

    expect(BuildDestination::execute('https://example.com/p?ref=deck', $link))
        ->toBe('https://example.com/p?ref=deck&utm_source=newsletter');
});

it('does not overrule a utm the destination url set itself', function () {
    $link = new Link(['utm_source' => 'newsletter']);

    expect(BuildDestination::execute('https://example.com/p?utm_source=deck', $link))
        ->toBe('https://example.com/p?utm_source=deck');
});

it('lets the incoming request win over both', function () {
    $link = new Link(['utm_source' => 'newsletter']);

    expect(BuildDestination::execute('https://example.com/p?utm_source=deck', $link, ['utm_source' => 'twitter']))
        ->toBe('https://example.com/p?utm_source=twitter');
});

it('leaves a url alone when there is nothing to add', function () {
    $link = new Link;

    expect(BuildDestination::execute('https://example.com/pricing', $link))
        ->toBe('https://example.com/pricing');
});

it('keeps the fragment after the query', function () {
    $link = new Link(['utm_campaign' => 'launch']);

    expect(BuildDestination::execute('https://example.com/docs#install', $link))
        ->toBe('https://example.com/docs?utm_campaign=launch#install');
});

it('returns anything that is not a url untouched', function () {
    $link = new Link(['utm_source' => 'newsletter']);

    expect(BuildDestination::execute('not-a-url', $link))->toBe('not-a-url');
});

it('keeps credentials and a port when it rebuilds the url', function () {
    $link = new Link(['utm_source' => 'newsletter']);

    // Both halves are rare but valid, and dropping either would send the
    // visitor somewhere that does not answer.
    expect(BuildDestination::execute('https://ada:secret@example.com:8443/deck', $link))
        ->toBe('https://ada:secret@example.com:8443/deck?utm_source=newsletter');
});

it('keeps a user with no password', function () {
    $link = new Link(['utm_source' => 'newsletter']);

    expect(BuildDestination::execute('https://ada@example.com/deck', $link))
        ->toBe('https://ada@example.com/deck?utm_source=newsletter');
});
