<?php

declare(strict_types=1);

use App\Actions\Invite\CreateInvite;
use App\Enums\User\Role;
use App\Mail\Team\SendUserInvite;
use App\Mcp\Servers\LuaServer;
use App\Mcp\Tools\Invite\CreateInviteTool;
use App\Mcp\Tools\Invite\DeleteInviteTool;
use App\Mcp\Tools\Invite\ListInvitesTool;
use App\Models\Invite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function () {
    Mail::fake();

    $this->user = User::factory()->withWorkspace()->create();
    $this->workspace = $this->user->currentWorkspace;
});

// --- the action ------------------------------------------------------------

it('emails an invite to someone without an account', function () {
    $invite = CreateInvite::execute($this->workspace, [
        'email' => 'newcomer@example.com',
        'role' => Role::ROLE_ADMIN->value,
    ]);

    expect($invite)->not->toBeNull()
        ->and($invite->workspace_id)->toBe($this->workspace->id);

    // The mailable is ShouldQueue, so Mail::fake records it as queued.
    Mail::assertQueued(SendUserInvite::class);
});

it('adds an existing account to the workspace instead of emailing it', function () {
    $existing = User::factory()->create(['email' => 'known@example.com']);

    // No invite to accept: they already have an account, so they simply join.
    $invite = CreateInvite::execute($this->workspace, [
        'email' => 'known@example.com',
        'role' => Role::ROLE_USER->value,
    ]);

    expect($invite)->toBeNull()
        ->and($existing->fresh()->workspaces->pluck('id'))->toContain($this->workspace->id);

    Mail::assertNothingQueued();
});

it('joins an existing account with the role it was given', function () {
    $existing = User::factory()->create(['email' => 'known@example.com']);

    CreateInvite::execute($this->workspace, [
        'email' => 'known@example.com',
        'role' => Role::ROLE_USER->value,
    ]);

    expect($existing->fresh()->workspaces->first()->membership->role)
        ->toBe(Role::ROLE_USER->value);
});

// --- the web surface -------------------------------------------------------

it('creates an invite from the settings screen', function () {
    $this->actingAs($this->user)
        ->post(route('setting.invites.store'), [
            'email' => 'newcomer@example.com',
            'role' => Role::ROLE_ADMIN->value,
        ])
        ->assertRedirect();

    expect(Invite::where('email', 'newcomer@example.com')
        ->where('workspace_id', $this->workspace->id)->exists())->toBeTrue();
});

it('refuses an invite without an email', function () {
    $this->actingAs($this->user)
        ->post(route('setting.invites.store'), ['email' => '', 'role' => Role::ROLE_ADMIN->value])
        ->assertSessionHasErrors('email');
});

it('refuses a second invite to the same address', function () {
    Invite::factory()->create([
        'workspace_id' => $this->workspace->id,
        'email' => 'newcomer@example.com',
    ]);

    $this->actingAs($this->user)
        ->post(route('setting.invites.store'), [
            'email' => 'newcomer@example.com',
            'role' => Role::ROLE_ADMIN->value,
        ])
        ->assertSessionHasErrors('email');
});

it('cancels an invite from the settings screen', function () {
    $invite = Invite::factory()->create(['workspace_id' => $this->workspace->id]);

    $this->actingAs($this->user)
        ->delete(route('setting.invites.destroy', $invite->id))
        ->assertRedirect();

    expect(Invite::find($invite->id))->toBeNull();
});

it('never cancels an invite belonging to another workspace', function () {
    $other = User::factory()->withWorkspace()->create();
    $invite = Invite::factory()->create(['workspace_id' => $other->current_workspace_id]);

    $this->actingAs($this->user)
        ->delete(route('setting.invites.destroy', $invite->id))
        ->assertNotFound();

    expect(Invite::find($invite->id))->not->toBeNull();
});

// --- MCP -------------------------------------------------------------------

it('creates an invite through the tool', function () {
    LuaServer::actingAs($this->user)
        ->tool(CreateInviteTool::class, [
            'email' => 'newcomer@example.com',
            'role' => Role::ROLE_ADMIN->value,
        ])
        ->assertOk();

    expect(Invite::where('email', 'newcomer@example.com')->exists())->toBeTrue();
});

it('lists only invites belonging to the bound workspace', function () {
    Invite::factory()->create([
        'workspace_id' => $this->workspace->id,
        'email' => 'mine@example.com',
    ]);

    $other = User::factory()->withWorkspace()->create();
    Invite::factory()->create([
        'workspace_id' => $other->current_workspace_id,
        'email' => 'theirs@example.com',
    ]);

    LuaServer::actingAs($this->user)
        ->tool(ListInvitesTool::class, [])
        ->assertOk()
        ->assertSee('mine@example.com')
        ->assertDontSee('theirs@example.com');
});

it('refuses to cancel an invite from another workspace through the tool', function () {
    $other = User::factory()->withWorkspace()->create();
    $invite = Invite::factory()->create(['workspace_id' => $other->current_workspace_id]);

    LuaServer::actingAs($this->user)
        ->tool(DeleteInviteTool::class, ['id' => $invite->id])
        ->assertHasErrors();

    expect(Invite::find($invite->id))->not->toBeNull();
});

// --- acceptance ------------------------------------------------------------

it('refuses an invite accepted with a different email', function () {
    $invite = Invite::factory()->create([
        'workspace_id' => $this->workspace->id,
        'email' => 'intended@example.com',
    ]);

    // This used to read ->id off the null it had just checked for, so a wrong
    // address was a fatal rather than a message.
    $this->post(route('auth.invites.accept', $invite->id), [
        'email' => 'someone.else@example.com',
        'name' => 'Someone Else',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertSessionHasErrors('email');

    expect(User::where('email', 'someone.else@example.com')->exists())->toBeFalse();
});

it('consumes the invite so the same link cannot be used twice', function () {
    $invite = Invite::factory()->create([
        'workspace_id' => $this->workspace->id,
        'email' => 'newcomer@example.com',
    ]);

    $this->post(route('auth.invites.accept', $invite->id), [
        'email' => 'newcomer@example.com',
        'name' => 'Newcomer',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    expect(Invite::find($invite->id))->toBeNull()
        ->and(User::where('email', 'newcomer@example.com')->exists())->toBeTrue();

    // Accepting logs you in, so the second attempt comes from someone else —
    // the invite is gone and the id no longer resolves.
    auth()->logout();

    $this->post(route('auth.invites.accept', $invite->id), [
        'email' => 'newcomer@example.com',
        'name' => 'Someone Else',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertSessionHasErrors('email');
});

it('refuses an invite for an address that already has an account', function () {
    User::factory()->create(['email' => 'known@example.com']);

    $invite = Invite::factory()->create([
        'workspace_id' => $this->workspace->id,
        'email' => 'known@example.com',
    ]);

    // Registering again would hit the unique index as a 500. Joining is what
    // they need, and CreateInvite does that for anyone who had an account when
    // the invite was written.
    $this->post(route('auth.invites.accept', $invite->id), [
        'email' => 'known@example.com',
        'name' => 'Known',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertSessionHasErrors('email');
});

it('lets the same address be invited to two different workspaces', function () {
    $elsewhere = User::factory()->withWorkspace()->create();

    Invite::factory()->create([
        'workspace_id' => $elsewhere->current_workspace_id,
        'email' => 'newcomer@example.com',
    ]);

    // The unique rule used to be global, so a pending invite anywhere blocked
    // this one — and told you it existed.
    $this->actingAs($this->user)
        ->post(route('setting.invites.store'), [
            'email' => 'newcomer@example.com',
            'role' => Role::ROLE_ADMIN->value,
        ])
        ->assertSessionHasNoErrors();

    expect(Invite::where('email', 'newcomer@example.com')->count())->toBe(2);
});

it('refuses an invite without a role', function () {
    // A missing role used to reach a NOT NULL column as a 500.
    $this->actingAs($this->user)
        ->post(route('setting.invites.store'), ['email' => 'newcomer@example.com'])
        ->assertSessionHasErrors('role');

    expect(Invite::count())->toBe(0);
});

it('builds the invitation email around the workspace and its link', function () {
    $invite = Invite::factory()->create([
        'workspace_id' => $this->workspace->id,
        'email' => 'newcomer@example.com',
    ]);

    $mail = new SendUserInvite($this->workspace, $invite);

    expect($mail->envelope()->subject)
        ->toBe("You are invited to join the {$this->workspace->name} team.")
        ->and($mail->content()->view)->toBe('mail.invite')
        ->and($mail->content()->with['url'])->toBe(route('auth.invites.show', $invite->id));

    // Rendering catches a broken blade, which the envelope alone would not.
    $mail->assertSeeInHtml($this->workspace->name);
});

it('sends the invitation on its own queue so email never blocks a request', function () {
    $invite = Invite::factory()->create(['workspace_id' => $this->workspace->id]);

    expect((new SendUserInvite($this->workspace, $invite))->queue)->toBe('emails');
});
