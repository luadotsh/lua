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

it('removes the avatar and the file behind it', function () {
    actingAs($this->user)->post(route('medias.store'), [
        'media' => UploadedFile::fake()->image('me.jpg', 200, 200),
        'collection' => 'avatar',
    ]);

    $path = Media::firstOrFail()->path;

    // This used to null a legacy `photo` column nothing reads, so the avatar
    // stayed exactly where it was and the file stayed on disk.
    actingAs($this->user)
        ->delete(route('setting.account.photo.destroy'))
        ->assertRedirect();

    expect($this->user->fresh()->photo_url)->toBeNull()
        ->and($this->user->fresh()->has_photo)->toBeFalse()
        ->and(Media::count())->toBe(0);

    Storage::assertMissing($path);
});

it('removes the workspace logo through the route that always existed', function () {
    actingAs($this->user)->post(route('medias.store'), [
        'media' => UploadedFile::fake()->image('logo.jpg', 200, 200),
        'collection' => 'logo',
    ]);

    $path = Media::firstOrFail()->path;

    // The route named a controller method that did not exist, so removing a
    // logo was a BadMethodCallException.
    actingAs($this->user)
        ->delete(route('setting.workspace.logo.destroy'))
        ->assertRedirect();

    expect($this->user->currentWorkspace->fresh()->logo_url)->toBeNull()
        ->and(Media::count())->toBe(0);

    Storage::assertMissing($path);
});

it('shrugs when there is no photo to remove', function () {
    actingAs($this->user)
        ->delete(route('setting.account.photo.destroy'))
        ->assertRedirect();

    expect(Media::count())->toBe(0);
});

it('refuses an upload larger than the limit', function () {
    actingAs($this->user)
        ->post(route('medias.store'), [
            'media' => UploadedFile::fake()->image('huge.jpg')->size(3000),
            'collection' => 'avatar',
        ])
        ->assertSessionHasErrors('media');

    expect(Media::count())->toBe(0);
});

it('refuses an upload with no file', function () {
    actingAs($this->user)
        ->post(route('medias.store'), ['collection' => 'avatar'])
        ->assertSessionHasErrors('media');
});

it('knows whether a collection holds anything', function () {
    expect($this->user->hasMedia('avatar'))->toBeFalse();

    actingAs($this->user)->post(route('medias.store'), [
        'media' => UploadedFile::fake()->image('me.jpg', 200, 200),
        'collection' => 'avatar',
    ]);

    expect($this->user->fresh()->hasMedia('avatar'))->toBeTrue();
});

it('keeps a gif as it is so the animation survives', function () {
    actingAs($this->user)->post(route('medias.store'), [
        'media' => UploadedFile::fake()->create('spin.gif', 10, 'image/gif'),
        'collection' => 'avatar',
    ]);

    // Re-encoding a GIF to JPEG would take the first frame and drop the rest.
    expect(Media::firstOrFail()->mime_type)->toBe('image/gif');
});
