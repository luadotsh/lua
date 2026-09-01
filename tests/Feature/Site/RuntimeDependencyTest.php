<?php

declare(strict_types=1);

use App\Actions\Blog\ListPosts;

/**
 * Production installs run `composer install --no-dev`, so anything the app
 * touches at runtime has to be declared in `require`. A package that arrives
 * only as a transitive dependency of a dev tool is present on every developer
 * machine and in every test run, and absent in production — which is why this
 * is asserted here rather than left to the rest of the suite to catch. It
 * cannot: the suite runs with the dev dependencies installed.
 *
 * This happened. `symfony/yaml` reached the app only through laravel/boost and
 * laravel/sail, both require-dev, and every blog post returned a 500 in
 * production while the whole suite stayed green.
 */
function composerManifest(): array
{
    $json = json_decode((string) file_get_contents(base_path('composer.json')), true);

    return is_array($json) ? $json : [];
}

it('declares the packages the blog renders posts with', function (): void {
    $require = data_get(composerManifest(), 'require', []);

    // RenderPost builds a League\CommonMark environment itself, and the
    // FrontMatterExtension it registers parses YAML through symfony/yaml.
    expect($require)->toHaveKey('league/commonmark')
        ->and($require)->toHaveKey('symfony/yaml');
});

it('keeps those packages out of require-dev', function (): void {
    $requireDev = data_get(composerManifest(), 'require-dev', []);

    expect($requireDev)->not->toHaveKey('league/commonmark')
        ->and($requireDev)->not->toHaveKey('symfony/yaml');
});

it('parses the frontmatter that the missing parser took down', function (): void {
    $post = ListPosts::execute()->first();

    expect($post)->not->toBeNull()
        ->and(data_get($post, 'title'))->not->toBeEmpty()
        ->and(data_get($post, 'date'))->not->toBeNull();
});
