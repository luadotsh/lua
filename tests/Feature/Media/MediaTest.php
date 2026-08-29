<?php

declare(strict_types=1);

use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake();
    $this->user = User::factory()->withWorkspace()->create();
});

it('stores an avatar and points the profile at it', function () {
    actingAs($this->user)
        ->post(route('medias.store'), [
            'media' => UploadedFile::fake()->image('me.jpg', 200, 200),
            'collection' => 'avatar',
        ])
        ->assertOk();

    $media = Media::firstOrFail();

    expect($media->collection)->toBe('avatar')
        ->and($media->mediable_id)->toBe($this->user->id)
        ->and($media->meta)->toMatchArray(['width' => 200, 'height' => 200]);

    Storage::assertExists($media->path);
    expect($this->user->fresh()->photo_url)->toBe($media->url);
});

it('converts a png to jpeg so it renders everywhere', function () {
    actingAs($this->user)
        ->post(route('medias.store'), [
            'media' => UploadedFile::fake()->image('logo.png', 120, 120),
            'collection' => 'logo',
        ])
        ->assertOk();

    $media = Media::firstOrFail();

    expect($media->mime_type)->toBe('image/jpeg')
        ->and($media->path)->toEndWith('.jpg');
});

it('replaces the previous file in a single-file collection', function () {
    foreach (['first.jpg', 'second.jpg'] as $name) {
        actingAs($this->user)->post(route('medias.store'), [
            'media' => UploadedFile::fake()->image($name),
            'collection' => 'avatar',
        ]);
    }

    expect(Media::count())->toBe(1)
        ->and(Media::first()->original_filename)->toBe('second.jpg');
});

it('rejects a collection it does not know', function () {
    actingAs($this->user)
        ->post(route('medias.store'), [
            'media' => UploadedFile::fake()->image('x.jpg'),
            'collection' => 'passport-scans',
        ])
        ->assertSessionHasErrors('collection');
});

it('rejects a file that is not an image', function () {
    actingAs($this->user)
        ->post(route('medias.store'), [
            'media' => UploadedFile::fake()->create('payload.php', 10, 'text/php'),
            'collection' => 'avatar',
        ])
        ->assertSessionHasErrors('media');
});

it('will not let one person delete another avatar', function () {
    $other = User::factory()->withWorkspace()->create();

    actingAs($other)->post(route('medias.store'), [
        'media' => UploadedFile::fake()->image('theirs.jpg'),
        'collection' => 'avatar',
    ]);

    $theirs = Media::firstOrFail();

    actingAs($this->user)
        ->delete(route('medias.destroy', $theirs))
        ->assertForbidden();

    expect(Media::find($theirs->id))->not->toBeNull();
});

it('deletes the file from storage along with the record', function () {
    actingAs($this->user)->post(route('medias.store'), [
        'media' => UploadedFile::fake()->image('mine.jpg'),
        'collection' => 'avatar',
    ]);

    $media = Media::firstOrFail();
    $path = $media->path;

    actingAs($this->user)->delete(route('medias.destroy', $media));

    expect(Media::find($media->id))->toBeNull();
    Storage::assertMissing($path);
});
