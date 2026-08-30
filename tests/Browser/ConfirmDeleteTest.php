<?php

declare(strict_types=1);

use App\Models\Domain;
use App\Models\Link;
use App\Models\Tag;
use App\Models\User;

/**
 * Every delete that cannot be undone asks you to type the thing's own name
 * first. Typing the name rather than a generic keyword is what makes you read
 * which one you are about to remove.
 */
test('deleting a tag stays disabled until its name is typed', function () {
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

    $page->type('@confirm-delete-input', 'Campaign')
        ->assertButtonEnabled('@confirm-delete-button');

    $page->click('@confirm-delete-button')
        ->assertDontSee('Campaign')
        ->assertNoJavaScriptErrors();

    expect(Tag::find($tag->id))->toBeNull();
});

test('deleting a domain asks for the domain itself', function () {
    $user = User::factory()->withWorkspace()->create();

    $domain = Domain::factory()->create([
        'workspace_id' => $user->currentWorkspace->id,
        'domain' => 'links.example.com',
    ]);

    $this->actingAs($user);

    $page = visit(route('setting.domains.index'));

    $page->click("@domain-delete-{$domain->id}")
        ->assertButtonDisabled('@confirm-delete-button');

    $page->type('@confirm-delete-input', 'links.example.com')
        ->click('@confirm-delete-button')
        ->assertNoJavaScriptErrors();

    expect(Domain::find($domain->id))->toBeNull();
});

test('deleting a link asks for the short link', function () {
    $user = User::factory()->withWorkspace()->create();

    $link = Link::factory()->create([
        'workspace_id' => $user->currentWorkspace->id,
    ]);

    $this->actingAs($user);

    $page = visit(route('links.edit', $link->id));

    $page->click('@link-delete')
        ->assertButtonDisabled('@confirm-delete-button');

    $page->type('@confirm-delete-input', $link->link)
        ->click('@confirm-delete-button')
        ->assertNoJavaScriptErrors();

    expect(Link::find($link->id))->toBeNull();
});
