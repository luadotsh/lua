<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { IconArrowRight, IconCheck } from '@tabler/icons-vue';
import { computed, ref } from 'vue';

import JsonLd from '@/components/site/JsonLd.vue';
import Seo from '@/components/site/Seo.vue';
import { Button } from '@/components/ui/button';
import SiteLayout from '@/layouts/site/SiteLayout.vue';
import { register } from '@/routes';

/**
 * Tiers come from the plans table, so the number quoted here is the one
 * charged at checkout. A hardcoded price on a marketing page is a promise the
 * billing code never made.
 */
type Tier = {
    name: string;
    internal_id: string;
    monthly_price: number;
    yearly_price: number | null;
    max_links: number;
    max_events: number;
    max_users: number;
    max_tags: number;
    max_domains: number;
};

const props = defineProps<{
    tiers: Tier[];
    seo: { title: string; description: string };
}>();

const yearly = ref(false);

// Free is not a tier people choose between; it is the way in. It sits under
// the paid plans so the comparison above it is between the four that are
// actually comparable.
const paid = computed(() =>
    props.tiers.filter((tier) => tier.monthly_price > 0),
);
const free = computed(() =>
    props.tiers.find((tier) => tier.monthly_price === 0),
);

const number = new Intl.NumberFormat('en-US');

// "1 tags" and "None custom domains" both read as a bug in the pricing, which
// is the last place to look careless.
const count = (value: number, singular: string, plural: string): string =>
    value === 0
        ? `No ${plural}`
        : `${number.format(value)} ${value === 1 ? singular : plural}`;

// The same phrase mid-sentence: "…a month, No custom domains" reads as a typo.
const countInline = (
    value: number,
    singular: string,
    plural: string,
): string => {
    const text = count(value, singular, plural);

    return text.charAt(0).toLowerCase() + text.slice(1);
};

const rows = (tier: Tier): string[] => [
    count(tier.max_links, 'link', 'links'),
    `${number.format(tier.max_events)} click events /mo`,
    count(tier.max_domains, 'custom domain', 'custom domains'),
    count(tier.max_tags, 'tag', 'tags'),
    count(tier.max_users, 'team member', 'team members'),
];

// Pro is where most teams land, so it carries the emphasis. One card, not two.
const isFeatured = (tier: Tier): boolean => tier.internal_id === 'pro-monthly';

// Shown per month either way, so the two columns of the toggle are comparable
// rather than asking the reader to divide by twelve.
const monthlyEquivalent = (tier: Tier): number =>
    yearly.value && tier.yearly_price !== null
        ? Math.round(tier.yearly_price / 12)
        : tier.monthly_price;

const savingMonths = computed(() => {
    const tier = paid.value.find(
        (candidate) => candidate.yearly_price !== null,
    );

    if (!tier || tier.yearly_price === null) {
        return 0;
    }

    return Math.round(12 - tier.yearly_price / tier.monthly_price);
});

/**
 * The full comparison.
 *
 * A row is either a number that differs by tier, or a feature that every tier
 * has. Only five things are actually metered by plan (links, events, domains,
 * tags, members), so most of this table is a column of ticks. That is not
 * padding, it is the page's argument made checkable: everything the product
 * does is on the free plan too, and the paid plans buy volume.
 *
 * Every row below exists in the product. Nothing is aspirational.
 */
type ComparisonRow = {
    label: string;
    /** Reads a per-tier number, or omitted when the row is on every plan. */
    value?: (tier: Tier) => string;
    note?: string;
};

const groups: Array<{ title: string; rows: ComparisonRow[] }> = [
    {
        title: 'Links',
        rows: [
            {
                label: 'Short links',
                value: (tier) => number.format(tier.max_links),
            },
            { label: 'Custom back-half' },
            { label: 'QR code per link' },
            { label: 'Expiry date and fallback URL' },
            { label: 'Password protection' },
            { label: 'Separate iOS and Android destinations' },
        ],
    },
    {
        title: 'Analytics',
        rows: [
            {
                label: 'Click events /mo',
                value: (tier) => number.format(tier.max_events),
            },
            { label: 'Country, region and city' },
            { label: 'Device, browser and OS' },
            { label: 'Referrer and UTM parameters' },
            { label: 'Language' },
            { label: 'QR scans counted separately' },
            {
                label: 'Any date range',
                note: 'History is kept, not rolled off',
            },
            { label: 'Filter by any dimension' },
        ],
    },
    {
        title: 'Domains',
        rows: [
            {
                label: 'Custom domains',
                // A bare 0 in a column of counts reads as a number rather than
                // as an absence.
                value: (tier) =>
                    tier.max_domains === 0
                        ? 'None'
                        : number.format(tier.max_domains),
            },
        ],
    },
    {
        title: 'Workspace',
        rows: [
            {
                label: 'Team members',
                value: (tier) => number.format(tier.max_users),
            },
            { label: 'Tags', value: (tier) => number.format(tier.max_tags) },
        ],
    },
    {
        title: 'Developers',
        rows: [
            { label: 'REST API' },
            { label: 'MCP server for AI agents' },
            { label: 'API tokens' },
        ],
    },
    {
        title: 'Hosting',
        rows: [
            { label: 'Self-host it yourself', note: 'Open source, any server' },
        ],
    },
];

// Free is a column in the table even though it sits below the cards: the table
// is reference, and leaving it out would be the one place the page hides a
// number.
const columns = computed<Tier[]>(() => [
    ...paid.value,
    ...(free.value ? [free.value] : []),
]);
</script>

<template>
    <Seo :title="seo.title" :description="seo.description" />
    <JsonLd
        :data="{
            '@type': 'WebPage',
            name: seo.title,
            description: seo.description,
        }"
    />

    <SiteLayout>
        <section class="px-6 py-16 sm:px-10 sm:py-24">
            <div class="max-w-2xl">
                <p class="label">Pricing</p>
                <h1
                    class="mt-2 font-display text-4xl font-semibold tracking-tight text-balance sm:text-5xl"
                >
                    Every plan sees every click
                </h1>
                <p class="mt-4 text-lg text-muted-foreground">
                    Analytics are not the upgrade. What you pay for is volume,
                    domains, and the size of your team.
                </p>
            </div>

            <!--
                A segmented control rather than a switch: a switch asks the
                reader to work out which side is on, and the saving has to be
                readable before they commit to reading the prices.
            -->
            <div class="mt-10 flex items-center gap-3">
                <div
                    class="inline-flex rounded-lg border border-border bg-card p-1"
                    role="group"
                    aria-label="Billing period"
                >
                    <button
                        type="button"
                        data-testid="billing-monthly"
                        class="rounded-md px-3 py-1.5 text-sm font-medium transition-colors"
                        :class="
                            yearly
                                ? 'text-muted-foreground hover:text-foreground'
                                : 'bg-primary text-primary-foreground'
                        "
                        :aria-pressed="!yearly"
                        @click="yearly = false"
                    >
                        Monthly
                    </button>
                    <button
                        type="button"
                        data-testid="billing-yearly"
                        class="rounded-md px-3 py-1.5 text-sm font-medium transition-colors"
                        :class="
                            yearly
                                ? 'bg-primary text-primary-foreground'
                                : 'text-muted-foreground hover:text-foreground'
                        "
                        :aria-pressed="yearly"
                        @click="yearly = true"
                    >
                        Yearly
                    </button>
                </div>

                <p
                    v-if="savingMonths > 0"
                    class="text-sm text-muted-foreground"
                >
                    {{ savingMonths }} months free on yearly
                </p>
            </div>

            <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="tier in paid"
                    :key="tier.internal_id"
                    :data-testid="`tier-${tier.internal_id}`"
                    class="site-card flex flex-col p-6"
                    :class="
                        isFeatured(tier)
                            ? 'border-primary/40 ring-1 ring-primary/20'
                            : 'border-border'
                    "
                >
                    <div class="flex items-center justify-between gap-2">
                        <h2 class="font-medium">{{ tier.name }}</h2>
                        <span
                            v-if="isFeatured(tier)"
                            class="rounded-full bg-accent px-2 py-0.5 text-xs font-medium text-accent-foreground"
                        >
                            Most picked
                        </span>
                    </div>

                    <p class="mt-4 flex items-baseline gap-1">
                        <span
                            class="font-display text-4xl font-semibold tracking-tight tabular-nums"
                        >
                            ${{ number.format(monthlyEquivalent(tier)) }}
                        </span>
                        <span class="text-sm text-muted-foreground">/mo</span>
                    </p>
                    <p class="mt-1 h-4 text-xs text-muted-foreground">
                        <template v-if="yearly && tier.yearly_price !== null">
                            ${{ number.format(tier.yearly_price) }} billed
                            yearly
                        </template>
                    </p>

                    <ul class="mt-6 mb-8 space-y-2.5 text-sm">
                        <li
                            v-for="row in rows(tier)"
                            :key="row"
                            class="flex gap-2.5"
                        >
                            <IconCheck
                                class="mt-0.5 size-4 shrink-0 text-primary-text"
                            />
                            <span>{{ row }}</span>
                        </li>
                    </ul>

                    <Button
                        class="mt-auto w-full"
                        :variant="isFeatured(tier) ? 'default' : 'outline'"
                        as-child
                    >
                        <Link :href="register.url()"
                            >Choose {{ tier.name }}</Link
                        >
                    </Button>
                </div>
            </div>

            <!--
                Free, quieter and underneath: one row, no card of its own, no
                feature list competing with the four above it.
            -->
            <div
                v-if="free"
                data-testid="tier-free"
                class="mt-4 flex flex-col gap-4 rounded-[0.875rem] border border-dashed border-border px-6 py-5 sm:flex-row sm:items-center"
            >
                <div class="min-w-0">
                    <p class="text-sm font-medium">
                        {{ free.name }}
                        <span class="text-muted-foreground">· $0 forever</span>
                    </p>
                    <p class="mt-1 text-sm text-muted-foreground">
                        {{ count(free.max_links, 'link', 'links') }},
                        {{ number.format(free.max_events) }} click events /mo,
                        {{
                            countInline(
                                free.max_domains,
                                'custom domain',
                                'custom domains',
                            )
                        }}. Every analytics field, same as the paid plans.
                    </p>
                </div>

                <Link
                    :href="register.url()"
                    class="group inline-flex shrink-0 items-center gap-1.5 text-sm font-medium sm:ml-auto"
                >
                    Start for free
                    <IconArrowRight
                        class="size-4 transition-transform group-hover:translate-x-0.5"
                    />
                </Link>
            </div>

            <div class="mt-20 border-t border-border pt-12">
                <p class="label">Compare</p>
                <h2
                    class="mt-2 font-display text-2xl font-semibold tracking-tight sm:text-3xl"
                >
                    What actually changes between plans
                </h2>
                <p class="mt-3 max-w-2xl text-muted-foreground">
                    Five numbers, and nothing else. Every feature below is on
                    every plan, the free one included.
                </p>

                <!--
                    Its own scroll container: five columns will not fit a phone,
                    and the page body must never scroll sideways.
                -->
                <div class="mt-8 overflow-x-auto">
                    <table
                        class="w-full min-w-[52rem] border-collapse text-left text-sm"
                    >
                        <thead>
                            <tr class="border-b border-border">
                                <th scope="col" class="py-3 pr-4 font-medium">
                                    Feature
                                </th>
                                <th
                                    v-for="tier in columns"
                                    :key="tier.internal_id"
                                    scope="col"
                                    class="w-[13%] px-4 py-3 text-center font-medium"
                                    :class="
                                        tier.monthly_price === 0
                                            ? 'text-muted-foreground'
                                            : ''
                                    "
                                >
                                    {{ tier.name }}
                                </th>
                            </tr>
                        </thead>

                        <tbody v-for="group in groups" :key="group.title">
                            <tr>
                                <th
                                    scope="colgroup"
                                    :colspan="columns.length + 1"
                                    class="pt-8 pb-2 text-xs font-medium tracking-wide text-primary-text uppercase"
                                >
                                    {{ group.title }}
                                </th>
                            </tr>
                            <tr
                                v-for="row in group.rows"
                                :key="row.label"
                                class="border-b border-border last:border-0"
                            >
                                <th scope="row" class="py-3 pr-4 font-normal">
                                    {{ row.label }}
                                    <span
                                        v-if="row.note"
                                        class="block text-xs text-muted-foreground"
                                    >
                                        {{ row.note }}
                                    </span>
                                </th>
                                <td
                                    v-for="tier in columns"
                                    :key="tier.internal_id"
                                    class="px-4 py-3 text-center tabular-nums"
                                    :class="
                                        tier.monthly_price === 0
                                            ? 'text-muted-foreground'
                                            : ''
                                    "
                                >
                                    <template v-if="row.value">{{
                                        row.value(tier)
                                    }}</template>
                                    <template v-else>
                                        <IconCheck
                                            class="mx-auto size-4 text-primary-text"
                                        />
                                        <span class="sr-only">Included</span>
                                    </template>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <p class="mt-10 max-w-2xl text-sm text-muted-foreground">
                    Lua is open source. If none of these fit, self-host it and
                    pay for nothing but your own server.
                </p>
            </div>
        </section>
    </SiteLayout>
</template>
