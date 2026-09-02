<?php

declare(strict_types=1);

use App\Actions\Link\CreateLink;

it('redirects a bare marketing path to the matching path on the marketing site', function (): void {
    $this->get('https://'.config('domains.main').'/pricing')
        ->assertRedirect(rtrim((string) config('app.website'), '/').'/pricing')
        ->assertStatus(301);
});

it('redirects a marketing slug path preserving the slug', function (): void {
    $this->get('https://'.config('domains.main').'/blog/what-a-short-link-actually-records')
        ->assertRedirect(rtrim((string) config('app.website'), '/').'/blog/what-a-short-link-actually-records')
        ->assertStatus(301);
});

it('redirects a tool path, which routes/site.php registered as an explicit top-level route rather than a slug', function (string $path): void {
    $this->get('https://'.config('domains.main')."/{$path}")
        ->assertRedirect(rtrim((string) config('app.website'), '/')."/{$path}")
        ->assertStatus(301);
})->with([
    'tools/utm-builder',
    'tools/qr-generator',
    'tools/link-checker',
]);

it('does not redirect a marketing path on a customer domain', function (): void {
    // The redirect group is scoped to the main domain; a customer's own
    // domain is free to use these words as short-link keys.
    $this->get('https://links.example.com/pricing')
        ->assertNotFound();
});

it('reserves the marketing words again now that they redirect', function (): void {
    expect(CreateLink::reservedKeys())
        ->toContain('pricing', 'faq', 'terms', 'privacy', 'blog', 'use-cases', 'glossary', 'tools', 'alternatives');
});
