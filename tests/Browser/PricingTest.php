<?php

declare(strict_types=1);

use App\Models\Plan;

/**
 * The billing toggle is the one interactive thing on the page, and the number
 * it swaps to is a price. Getting it wrong quotes a figure the checkout will
 * not honour, so the expected values are read from the plans table rather
 * than written into the test.
 */
it('swaps to the monthly equivalent of the yearly price', function (): void {
    $pro = Plan::where('internal_id', 'pro-monthly')->firstOrFail();
    $proYearly = Plan::where('internal_id', 'pro-yearly')->firstOrFail();

    $perMonth = (int) round($proYearly->price / 12);

    visit(route('site.pricing'))
        ->on()->desktop()
        ->assertSee('$'.number_format((float) $pro->price))
        // Server-rendered: the control is in the markup before Vue attaches a
        // listener, so a click landing in that window does nothing and the
        // assertion after it times out. The Webpage API exposes no predicate
        // wait, so this is an explicit margin for hydration.
        ->wait(1)
        ->click('@billing-yearly')
        ->assertSee('$'.number_format($perMonth))
        ->assertSee('$'.number_format((float) $proYearly->price).' billed yearly')
        ->click('@billing-monthly')
        ->assertSee('$'.number_format((float) $pro->price))
        ->assertNoJavaScriptErrors();
});

it('puts free below the paid tiers, not among them', function (): void {
    // The ordering is the ask: free is the way in, not one of the options
    // being weighed against each other.
    $order = visit(route('site.pricing'))
        ->on()->desktop()
        ->script(<<<'JS'
        function () {
            const position = (selector) =>
                document.querySelector(selector).getBoundingClientRect().top;

            return {
                free: position('[data-testid="tier-free"]'),
                lastPaid: position('[data-testid="tier-scale-monthly"]'),
            };
        }
        JS);

    expect($order['free'])->toBeGreaterThan($order['lastPaid']);
});

it('shows every feature as included on the free tier too', function (): void {
    // The page claims the paid plans buy volume and nothing else. If a feature
    // row ever becomes plan-gated, this is where the claim stops being true.
    $ticks = visit(route('site.pricing'))
        ->on()->desktop()
        ->script(<<<'JS'
        function () {
            const rows = [...document.querySelectorAll('tbody tr')].filter(
                (row) => row.querySelector('th[scope="row"]'),
            );

            return rows
                .filter((row) => row.querySelector('svg'))
                .map((row) => {
                    const cells = [...row.querySelectorAll('td')];

                    return cells.every((cell) => cell.querySelector('svg') !== null);
                })
                .filter((allTicked) => allTicked === false).length;
        }
        JS);

    expect($ticks)->toBe(0);
});
