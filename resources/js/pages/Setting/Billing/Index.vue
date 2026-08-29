<script setup lang="ts">
import { Head, usePage } from "@inertiajs/vue3";
import {
    IconClick,
    IconInfoCircle,
    IconLink,
    IconTag,
    IconUsers,
    IconWorld,
} from "@tabler/icons-vue";
import { computed, ref } from "vue";
import UsageChart from "@/components/billing/UsageChart.vue";
import UsageMeter from "@/components/billing/UsageMeter.vue";
import { Button } from "@/components/ui/button";
import AppLayout from "@/layouts/AppLayout.vue";
import SettingsLayout from "@/layouts/settings/Layout.vue";
import { formatNumber } from "@/lib/metrics";
import { cn } from "@/lib/utils";
import * as billingRoutes from "@/routes/setting/billing";
import type { WorkspaceUsage } from "@/types";

const usage = computed(() => usePage().props.usage as WorkspaceUsage);

type SeriesKey = "links" | "events";

// The two headline meters double as the chart's selector, the same way the
// analytics header picks the series below it.
const series = ref<SeriesKey>("links");

const headline = computed(() => [
    {
        key: "links" as SeriesKey,
        label: "Links created",
        icon: IconLink,
        ...usage.value.links,
    },
    {
        key: "events" as SeriesKey,
        label: "Events tracked",
        icon: IconClick,
        ...usage.value.events,
    },
]);

const chart = computed(() => usage.value[series.value].chart.chart);
const chartTitle = computed(() => (series.value === "links" ? "Links" : "Events"));

const nextTier = computed(() => usage.value.plan.next_tier);

const upgradeUrl = computed(() =>
    usage.value.billing.has_subscription
        ? billingRoutes.portal.url()
        : billingRoutes.upgrade.url(),
);
</script>

<template>
    <Head title="Billing" />

    <AppLayout>
        <SettingsLayout
            title="Billing"
            description="Your plan, usage and invoices."
        >
            <div class="flex flex-col gap-4">
                <div
                    class="flex flex-col gap-3 rounded-lg border border-border bg-card p-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div class="flex flex-col gap-0.5">
                        <span class="text-sm font-medium text-foreground">
                            You are on the {{ usage.plan.name }} plan
                        </span>
                        <span class="text-sm text-muted-foreground">
                            Current billing cycle:
                            {{ usage.current_billing_cycle_formatted }}
                        </span>
                    </div>

                    <Button
                        v-if="usage.billing.has_subscription"
                        as="a"
                        variant="outline"
                        :href="billingRoutes.portal.url()"
                    >
                        Manage subscription
                    </Button>
                </div>

                <div
                    class="grid grid-cols-1 gap-px overflow-hidden rounded-lg border border-border bg-border sm:grid-cols-2"
                >
                    <button
                        v-for="metric in headline"
                        :key="metric.key"
                        type="button"
                        :aria-pressed="series === metric.key"
                        :class="cn(
                            'flex cursor-pointer flex-col gap-3 bg-card p-4 text-left transition-colors',
                            'hover:bg-accent focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-inset',
                            series === metric.key && 'bg-accent',
                        )"
                        @click="series = metric.key"
                    >
                        <div class="flex items-center gap-2">
                            <component :is="metric.icon" class="size-4 text-muted-foreground" />
                            <span class="text-xs font-medium tracking-wide text-muted-foreground uppercase">
                                {{ metric.label }}
                            </span>
                        </div>

                        <span class="text-2xl font-semibold tabular-nums text-foreground">
                            {{ formatNumber(metric.used) }}
                        </span>

                        <div class="flex flex-col gap-1.5">
                            <div class="overflow-hidden rounded-full bg-muted">
                                <div
                                    class="h-1 rounded-full bg-gradient-to-r from-violet-400 to-violet-600 transition-all"
                                    :style="{ width: `${Math.min(metric.percent, 100)}%` }"
                                />
                            </div>
                            <span class="text-xs tabular-nums text-muted-foreground">
                                {{ formatNumber(metric.remaining) }} remaining of
                                {{ formatNumber(metric.limit) }}
                            </span>
                        </div>
                    </button>
                </div>

                <UsageChart
                    :title="chartTitle"
                    :labels="chart.labels"
                    :data="chart.data"
                />

                <div
                    class="grid grid-cols-1 gap-px overflow-hidden rounded-lg border border-border bg-border sm:grid-cols-3"
                >
                    <UsageMeter
                        :icon="IconTag"
                        label="Tags"
                        :used="usage.tags.used"
                        :remaining="usage.tags.remaining"
                        :limit="usage.tags.limit"
                        :percent="usage.tags.percent"
                    />
                    <UsageMeter
                        :icon="IconWorld"
                        label="Domains"
                        :used="usage.domains.used"
                        :remaining="usage.domains.remaining"
                        :limit="usage.domains.limit"
                        :percent="usage.domains.percent"
                    />
                    <UsageMeter
                        :icon="IconUsers"
                        label="Users"
                        :used="usage.users.used"
                        :remaining="usage.users.remaining"
                        :limit="usage.users.limit"
                        :percent="usage.users.percent"
                    />
                </div>

                <div
                    class="flex flex-col gap-3 rounded-lg border border-border bg-card p-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div class="flex items-center gap-2">
                        <IconInfoCircle class="size-4 shrink-0 text-muted-foreground" />
                        <span class="text-sm text-muted-foreground">
                            <template v-if="nextTier">
                                For higher limits, upgrade to the {{ nextTier.name }} plan.
                            </template>
                            <template v-else>
                                You are on {{ usage.plan.name }}, the highest limits we offer.
                            </template>
                        </span>
                    </div>

                    <Button v-if="nextTier" as="a" :href="upgradeUrl">Upgrade</Button>
                    <Button
                        v-else-if="usage.billing.has_subscription"
                        as="a"
                        variant="outline"
                        :href="billingRoutes.portal.url()"
                    >
                        Manage subscription
                    </Button>
                </div>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
