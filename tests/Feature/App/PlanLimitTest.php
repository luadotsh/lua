<?php

declare(strict_types=1);

use App\Actions\Domain\CreateDomain;
use App\Actions\Link\CreateLink;
use App\Actions\Tag\CreateTag;
use App\Models\Domain;
use App\Models\Link;
use App\Models\Plan;
use App\Models\Tag;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->withWorkspace()->create();
    $this->workspace = $this->user->currentWorkspace;
});

function planWith(array $limits): Plan
{
    return Plan::factory()->create($limits);
}

it('lets a workspace create links up to its allowance', function () {
    $this->workspace->update(['plan_id' => planWith(['max_links' => 5])->id]);

    foreach (range(1, 5) as $i) {
        CreateLink::execute($this->workspace, ['url' => "https://example.com/{$i}"]);
    }

    expect(Link::where('workspace_id', $this->workspace->id)->count())->toBe(5);
});

it('refuses the link past the allowance', function () {
    $this->workspace->update(['plan_id' => planWith(['max_links' => 5])->id]);

    Link::factory()->count(5)->create(['workspace_id' => $this->workspace->id]);

    // Enforced in the action, so the API and the MCP tool are covered by the
    // same check the web form goes through.
    expect(fn () => CreateLink::execute($this->workspace, ['url' => 'https://example.com/six']))
        ->toThrow(ValidationException::class, 'Your plan covers 5 links');

    expect(Link::where('workspace_id', $this->workspace->id)->count())->toBe(5);
});

it('refuses a custom domain on a plan that includes none', function () {
    $this->workspace->update(['plan_id' => planWith(['max_domains' => 0])->id]);

    // "0 of 0 used" is not what went wrong, so the message says what is true:
    // the plan does not include custom domains.
    expect(fn () => CreateDomain::execute($this->workspace, ['domain' => 'links.example.com']))
        ->toThrow(ValidationException::class, 'not included in your plan');

    expect(Domain::where('workspace_id', $this->workspace->id)->count())->toBe(0);
});

it('lets a paid plan connect a custom domain', function () {
    $this->workspace->update(['plan_id' => planWith(['max_domains' => 3])->id]);

    CreateDomain::execute($this->workspace, ['domain' => 'links.example.com']);

    expect(Domain::where('workspace_id', $this->workspace->id)->count())->toBe(1);
});

it('refuses the domain past the allowance', function () {
    $this->workspace->update(['plan_id' => planWith(['max_domains' => 1])->id]);

    Domain::factory()->create(['workspace_id' => $this->workspace->id]);

    expect(fn () => CreateDomain::execute($this->workspace, ['domain' => 'second.example.com']))
        ->toThrow(ValidationException::class, 'Your plan covers 1 custom domains');
});

it('ships a free workspace five links and no custom domains', function () {
    // PlanSeeder already ran: TestCase seeds before every test.
    $free = Plan::where('internal_id', 'free')->first();

    expect($free->max_links)->toBe(5)
        ->and($free->max_domains)->toBe(0);
});

it('refuses the tag past the allowance', function () {
    $this->workspace->update(['plan_id' => planWith(['max_tags' => 1])->id]);

    Tag::factory()->create(['workspace_id' => $this->workspace->id]);

    expect(fn () => CreateTag::execute($this->workspace, ['name' => 'Second', 'color' => '#000000']))
        ->toThrow(ValidationException::class, 'Your plan covers one tag');
});

it('seeds no more default tags than the plan allows', function (int $allowance, int $expected) {
    $plan = planWith(['max_tags' => $allowance]);

    // Seeding the full set regardless would hand the workspace a limit it is
    // already over, unable to create a tag and with nothing saying why.
    $workspace = Workspace::factory()->create(['plan_id' => $plan->id]);

    expect(Tag::where('workspace_id', $workspace->id)->count())->toBe($expected);
})->with([
    'free' => [1, 1],
    'paid' => [25, 3],
    'none' => [0, 0],
]);

it('ships a free workspace one tag and a hundred events', function () {
    $free = Plan::where('internal_id', 'free')->first();

    expect($free->max_tags)->toBe(1)
        ->and($free->max_events)->toBe(100);
});
