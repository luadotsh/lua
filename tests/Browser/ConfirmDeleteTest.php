<?php

declare(strict_types=1);

use App\Models\Domain;
use App\Models\Link;
use App\Models\Tag;
use App\Models\User;

/**
 * Every delete that cannot be undone asks you to type "delete" first. The point
 * is to break the rhythm of clicking through, so it is the same word on every
 * screen rather than a name already sitting beside the button.
 */
test('deleting a tag stays disabled until the keyword is typed', function () {
    $user = User::factory()->withWorkspace()->create();

    $tag = Tag::factory()->create([
        'workspace_id' => $user->currentWorkspace->id,
        'name' => 'Campaign',
    ]);

    $this->actingAs($user);

    $page = visit(route('setting.tags.index'));

    $page->click("@tag-delete-{$tag->id}")
        ->assertSee('Type')
        ->assertButtonDisabled('@confirm-delete-button');

    $page->type('@confirm-delete-input', 'wrong')
        ->assertButtonDisabled('@confirm-delete-button');

    $page->type('@confirm-delete-input', 'delete')
        ->assertButtonEnabled('@confirm-delete-button');

    $page->click('@confirm-delete-button')
        ->assertDontSee('Campaign')
        ->assertNoJavaScriptErrors();

    expect(Tag::find($tag->id))->toBeNull();
});

test('deleting a domain asks for the keyword', function () {
    $user = User::factory()->withWorkspace()->create();

    $domain = Domain::factory()->create([
        'workspace_id' => $user->currentWorkspace->id,
        'domain' => 'links.example.com',
    ]);

    $this->actingAs($user);

    $page = visit(route('setting.domains.index'));

    $page->click("@domain-delete-{$domain->id}")
        ->assertButtonDisabled('@confirm-delete-button');

    $page->type('@confirm-delete-input', 'delete')
        ->click('@confirm-delete-button')
        ->assertNoJavaScriptErrors();

    expect(Domain::find($domain->id))->toBeNull();
});

test('deleting a link asks for the keyword', function () {
    $user = User::factory()->withWorkspace()->create();

    $link = Link::factory()->create([
        'workspace_id' => $user->currentWorkspace->id,
    ]);

    $this->actingAs($user);

    $page = visit(route('links.edit', $link->id));

    $page->click('@link-delete')
        ->assertButtonDisabled('@confirm-delete-button');

    $page->type('@confirm-delete-input', 'delete')
        ->click('@confirm-delete-button')
        ->assertNoJavaScriptErrors();

    expect(Link::find($link->id))->toBeNull();
});
