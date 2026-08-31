<script setup lang="ts">
import { computed } from 'vue';
import { browserIconUrl } from '@/lib/browsers';
import { countryFlagUrl, countryFor } from '@/lib/countries';

/**
 * The analytics screen, drawn rather than screenshotted.
 *
 * A screenshot goes stale the first time the UI moves and ships a 300KB PNG
 * that a crawler cannot read. This is markup: it restyles with the theme,
 * stays sharp on any display, and uses the same country and browser icons the
 * real screen does — so what a visitor sees here is what they get.
 *
 * The numbers are illustrative and deliberately unremarkable. Inflating them
 * would be inventing a case study.
 */
const metrics = [
    { key: 'events', label: 'Events', value: '48,204' },
    { key: 'clicks', label: 'Clicks', value: '41,867' },
    { key: 'scans', label: 'QR scans', value: '6,337' },
    { key: 'visitors', label: 'Visitors', value: '29,512' },
];

// A month of clicks. Hand-picked rather than random so the shape stays the
// same between renders and the server and client agree — a random series
// would be a hydration mismatch.
const series = [
    38, 44, 41, 52, 61, 49, 43, 58, 72, 68, 64, 79, 91, 84, 77, 88, 103, 97, 92,
    108, 121, 114, 106, 118, 132, 127, 119, 138, 151, 144,
];

const chart = computed(() => {
    const width = 640;
    const height = 160;
    const max = Math.max(...series);
    const step = width / (series.length - 1);

    const points = series.map((value, index) => {
        const x = index * step;
        const y = height - (value / max) * (height - 12) - 6;

        return `${x.toFixed(1)},${y.toFixed(1)}`;
    });

    return {
        width,
        height,
        line: `M ${points.join(' L ')}`,
        area: `M 0,${height} L ${points.join(' L ')} L ${width},${height} Z`,
    };
});

const countries = [
    { code: 'US', value: 14203, share: 100 },
    { code: 'BR', value: 9841, share: 69 },
    { code: 'DE', value: 6120, share: 43 },
    { code: 'GB', value: 4877, share: 34 },
    { code: 'JP', value: 3106, share: 22 },
];

const referrers = [
    { name: 'instagram.com', value: 12408, share: 100 },
    { name: 'Direct', value: 9932, share: 80 },
    { name: 'newsletter', value: 7215, share: 58 },
    { name: 'x.com', value: 4180, share: 34 },
];

const browsers = [
    { name: 'chrome', label: 'Chrome', value: 18902, share: 100 },
    { name: 'safari', label: 'Safari', value: 12744, share: 67 },
    { name: 'firefox', label: 'Firefox', value: 4318, share: 23 },
    { name: 'edge', label: 'Edge', value: 2903, share: 15 },
];

const number = new Intl.NumberFormat('en-US');
</script>

<template>
    <div
        data-testid="analytics-mockup"
        class="overflow-hidden site-card shadow-sm"
    >
        <!-- Window chrome: enough to read as an application, not so much that
             it competes with what is inside it. -->
        <div class="flex items-center gap-3 border-b border-border px-4 py-3">
            <div class="flex gap-1.5" aria-hidden="true">
                <span class="size-2.5 rounded-full bg-border" />
                <span class="size-2.5 rounded-full bg-border" />
                <span class="size-2.5 rounded-full bg-border" />
            </div>
            <p class="truncate font-mono text-xs text-muted-foreground">
                go.example.com/spring
            </p>
            <span
                class="ml-auto inline-flex shrink-0 items-center gap-1.5 text-xs text-muted-foreground"
            >
                <span class="relative inline-flex size-1.5">
                    <span
                        class="absolute inset-0 inline-flex animate-ping rounded-full bg-emerald-500 opacity-75 motion-reduce:animate-none"
                    />
                    <span class="relative inline-flex size-1.5 rounded-full bg-emerald-500" />
                </span>
                Live
            </span>
        </div>

        <!-- Metric row. `tabular-nums` so the digits stay in their columns. -->
        <div class="grid grid-cols-2 divide-x divide-y divide-border sm:grid-cols-4 sm:divide-y-0">
            <div v-for="metric in metrics" :key="metric.key" class="px-4 py-4">
                <p class="text-xs text-muted-foreground">{{ metric.label }}</p>
                <p class="mt-1 text-2xl font-semibold tracking-tight tabular-nums">
                    {{ metric.value }}
                </p>
            </div>
        </div>

        <div class="border-t border-border px-4 py-5">
            <svg
                :viewBox="`0 0 ${chart.width} ${chart.height}`"
                class="h-40 w-full"
                preserveAspectRatio="none"
                aria-hidden="true"
            >
                <defs>
                    <linearGradient id="lua-chart-fill" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="currentColor" stop-opacity="0.18" />
                        <stop offset="100%" stop-color="currentColor" stop-opacity="0" />
                    </linearGradient>
                </defs>

                <path :d="chart.area" fill="url(#lua-chart-fill)" class="text-primary-text" />
                <path
                    :d="chart.line"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linejoin="round"
                    stroke-linecap="round"
                    vector-effect="non-scaling-stroke"
                    class="text-primary-text"
                />
            </svg>
        </div>

        <div class="grid divide-y divide-border border-t border-border sm:grid-cols-3 sm:divide-x sm:divide-y-0">
            <!--
                Each row carries a bar behind the label rather than beside it,
                which is what makes a list of numbers readable at a glance.
            -->
            <div class="p-4">
                <p class="text-xs font-medium text-muted-foreground">Countries</p>
                <ul class="mt-3 space-y-1">
                    <li
                        v-for="country in countries"
                        :key="country.code"
                        class="relative flex items-center gap-2 rounded px-2 py-1.5 text-sm"
                    >
                        <span
                            class="absolute inset-y-0 left-0 rounded bg-muted"
                            :style="{ width: `${country.share}%` }"
                            aria-hidden="true"
                        />
                        <img
                            :src="countryFlagUrl(country.code)"
                            alt=""
                            aria-hidden="true"
                            class="relative h-3 w-[18px] shrink-0 rounded-[2px] object-cover"
                            loading="lazy"
                        />
                        <span class="relative truncate">{{ countryFor(country.code).name }}</span>
                        <span class="relative ml-auto shrink-0 text-xs text-muted-foreground tabular-nums">
                            {{ number.format(country.value) }}
                        </span>
                    </li>
                </ul>
            </div>

            <div class="p-4">
                <p class="text-xs font-medium text-muted-foreground">Referrers</p>
                <ul class="mt-3 space-y-1">
                    <li
                        v-for="referrer in referrers"
                        :key="referrer.name"
                        class="relative flex items-center gap-2 rounded px-2 py-1.5 text-sm"
                    >
                        <span
                            class="absolute inset-y-0 left-0 rounded bg-muted"
                            :style="{ width: `${referrer.share}%` }"
                            aria-hidden="true"
                        />
                        <span class="relative truncate">{{ referrer.name }}</span>
                        <span class="relative ml-auto shrink-0 text-xs text-muted-foreground tabular-nums">
                            {{ number.format(referrer.value) }}
                        </span>
                    </li>
                </ul>
            </div>

            <div class="p-4">
                <p class="text-xs font-medium text-muted-foreground">Browsers</p>
                <ul class="mt-3 space-y-1">
                    <li
                        v-for="browser in browsers"
                        :key="browser.name"
                        class="relative flex items-center gap-2 rounded px-2 py-1.5 text-sm"
                    >
                        <span
                            class="absolute inset-y-0 left-0 rounded bg-muted"
                            :style="{ width: `${browser.share}%` }"
                            aria-hidden="true"
                        />
                        <img
                            :src="browserIconUrl(browser.name)"
                            alt=""
                            aria-hidden="true"
                            class="relative size-4 shrink-0"
                            loading="lazy"
                        />
                        <span class="relative truncate">{{ browser.label }}</span>
                        <span class="relative ml-auto shrink-0 text-xs text-muted-foreground tabular-nums">
                            {{ number.format(browser.value) }}
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
