<?php

declare(strict_types=1);

use App\Actions\Blog\ListPosts;
use App\Actions\Link\CreateLink;
use Illuminate\Support\Facades\File;
use Inertia\Testing\AssertableInertia as Assert;

/**
 * Writes a post into the real content directory and removes it afterwards.
 * The directory is the database here, so a test that does not write a file is
 * not exercising the thing that publishes a post.
 */
function writePost(string $slug, string $frontmatter, string $body = "## A heading\n\nSome words.\n"): string
{
    $path = resource_path("blog/{$slug}.md");

    File::put($path, "---\n{$frontmatter}\n---\n\n{$body}");

    return $path;
}

afterEach(function (): void {
    foreach (File::glob(resource_path('blog/zz-test-*.md')) as $path) {
        File::delete($path);
    }
});

it('lists the published posts, newest first', function (): void {
    $this->get(route('site.blog.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Site/Blog/Index')
            ->has('posts')
            ->has('posts.0.slug')
            ->has('posts.0.title')
            ->has('posts.0.reading_time')
            ->where('posts', fn ($posts) => collect($posts)->pluck('date')->sortDesc()->values()->all()
                === collect($posts)->pluck('date')->values()->all())
        );
});

it('renders a post with its html and headings', function (): void {
    $this->get(route('site.blog.show', 'what-a-short-link-actually-records'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Site/Blog/Show')
            ->where('post.slug', 'what-a-short-link-actually-records')
            ->has('post.html')
            ->has('post.headings')
            ->has('seo.title')
        );
});

// The anchor and the contents entry come from one pass for exactly this
// reason: two slug functions is how a contents list points at nothing.
it('anchors every heading it reports', function (): void {
    $post = ListPosts::find('what-a-short-link-actually-records');

    expect($post['headings'])->not->toBeEmpty();

    foreach ($post['headings'] as $heading) {
        expect($post['html'])->toContain("id=\"{$heading['id']}\"");
    }
});

it('gives two headings with the same words distinct anchors', function (): void {
    writePost('zz-test-dupes', "title: Dupes\ndate: 2020-01-01", "## Same\n\na\n\n## Same\n\nb\n");

    $post = ListPosts::find('zz-test-dupes');
    $ids = collect($post['headings'])->pluck('id');

    expect($ids->all())->toBe(['same', 'same-2'])
        ->and($ids->duplicates())->toBeEmpty();
});

it('404s an unknown post', function (): void {
    $this->get(route('site.blog.show', 'no-such-post'))->assertNotFound();
});

// Writing ahead is the normal way to work, so a future date has to hold the
// post back on both surfaces rather than only hiding it from the listing.
it('keeps a post dated in the future out of the listing and off its own url', function (): void {
    writePost('zz-test-future', 'title: Not yet'.PHP_EOL.'date: 2037-12-31');

    $this->get(route('site.blog.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('posts', fn ($posts) => ! collect($posts)->pluck('slug')->contains('zz-test-future'))
        );

    $this->get(route('site.blog.show', 'zz-test-future'))->assertNotFound();
});

it('keeps a draft out of the listing and off its own url', function (): void {
    writePost('zz-test-draft', "title: Draft\ndate: 2020-01-01\ndraft: true");

    $this->get(route('site.blog.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('posts', fn ($posts) => ! collect($posts)->pluck('slug')->contains('zz-test-draft'))
        );

    $this->get(route('site.blog.show', 'zz-test-draft'))->assertNotFound();
});

it('publishes a post the moment its date arrives', function (): void {
    writePost('zz-test-today', 'title: Today'.PHP_EOL.'date: '.now()->toDateString());

    $this->get(route('site.blog.show', 'zz-test-today'))->assertOk();
});

it('reserves the blog paths as short-link back-halves', function (): void {
    expect(CreateLink::reservedKeys())->toContain('blog');
});
