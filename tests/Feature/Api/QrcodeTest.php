<?php

declare(strict_types=1);

use App\Actions\Link\GenerateQrCode;
use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->withWorkspace()->create();
    $this->link = Link::factory()->create(['workspace_id' => $this->user->current_workspace_id]);
});

it('returns a png for a link', function () {
    $this->get(route('api.qr-code', $this->link->id))
        ->assertOk()
        ->assertHeader('Content-Type', 'image/png');
});

it('encodes the link, with the suffix that marks a scan', function () {
    // RedirectController reads ?qr=1 to tell a scan from a click, so the code
    // is generated from the suffixed url rather than the bare short link.
    $qr = GenerateQrCode::execute($this->link);

    expect($qr)->not->toBeEmpty()
        ->and(str_starts_with($qr, "\x89PNG"))->toBeTrue();
});

it('draws the qr in the colour asked for', function () {
    $default = $this->get(route('api.qr-code', $this->link->id))->getContent();

    $coloured = $this->get(route('api.qr-code', [$this->link->id, 'color' => '#8b5cf6']))
        ->assertOk()
        ->getContent();

    expect($coloured)->not->toBe($default);
});

it('refuses a colour that is not a hex value', function () {
    $this->get(route('api.qr-code', [$this->link->id, 'color' => 'octarine']))
        ->assertStatus(302);
});

it('offers the qr as a download when asked', function () {
    $this->get(route('api.qr-code', [$this->link->id, 'download' => true]))
        ->assertOk()
        ->assertHeader('Content-Type', 'image/png')
        ->assertDownload('qr-code.png');
});

it('404s for a link that does not exist', function () {
    $this->get(route('api.qr-code', '00000000-0000-0000-0000-000000000000'))
        ->assertNotFound();
});
