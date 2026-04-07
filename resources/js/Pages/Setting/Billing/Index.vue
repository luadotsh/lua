<script setup lang="ts">
import { ref, computed, onMounted, reactive } from "vue";
import { Head, usePage, Link } from "@inertiajs/vue3";
import {
    IconInfoCircle,
    IconLink,
    IconClick,
    IconTag,
    IconWorld,
    IconUsers,
} from "@tabler/icons-vue";
import AppLayout from "@/layouts/AppLayout.vue";
import SettingsLayout from "@/layouts/settings/Layout.vue";
import { Button } from "@/components/ui/button";
import Chart from "@/components/Chart.vue";
import * as billingRoutes from "@/routes/setting/billing";

const usage = computed(() => usePage().props.usage);

const currentChart = ref("links");

const chartDataLinks = reactive({
    total: 0,
    chart: {
        label: "",
        data: [] as number[],
        labels: [] as string[],
    },
});

const chartDataEvents = reactive({
    total: 0,
    chart: {
        label: "",
        data: [] as number[],
        labels: [] as string[],
    },
});

onMounted(() => {
    // links
    chartDataLinks.total = usage.value.links.chart.total;
    chartDataLinks.chart = usage.value.links.chart.chart;

    // events
    chartDataEvents.total = usage.value.events.chart.total;
    chartDataEvents.chart = usage.value.events.chart.chart;
});

const links = computed(() => {
    return `${usage.value.links.remaining} remaining of ${usage.value.links.limit}`;
});

const events = computed(() => {
    return `${usage.value.events.remaining} remaining of ${usage.value.events.limit}`;
});

const users = computed(() => {
    return `${usage.value.users.remaining} remaining of ${usage.value.users.limit}`;
});

const domains = computed(() => {
    return `${usage.value.domains.remaining} remaining of ${usage.value.domains.limit}`;
});

const tags = computed(() => {
    return `${usage.value.tags.remaining} remaining of ${usage.value.tags.limit}`;
});
</script>

<template>
    <Head title="Plan and Usage" />

    <AppLayout>
        <SettingsLayout>
            <div class="space-y-4">
                <div
                    class="border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden px-6 py-8 text-zinc-800 dark:text-zinc-300"
                >
                    <div class="flex flex-col justify-center space-y-4">
                        <div class="sm:flex sm:items-center">
                            <div class="sm:flex-auto">
                                <h1 class="text-lg font-semibold">Plan and Usage</h1>
                            </div>

                            <a
                                v-if="usage.billing.has_subscription"
                                :href="billingRoutes.portal.url()"
                                class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2"
                            >
                                Manage Subscription
                            </a>
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                You are currently on the <b>{{ usage.plan.name }}</b
                                >. Current billing cycle:
                                <b>{{ usage.current_billing_cycle_formatted }}</b>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mx-auto grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div
                        @click="currentChart = 'links'"
                        :class="{
                            'gap-x-4 gap-y-2 bg-white dark:bg-zinc-800 p-6 space-y-4 rounded-lg overflow-hidden cursor-pointer': true,
                            'border border-zinc-200 dark:border-zinc-700':
                                currentChart !== 'links',
                            'border-2 border-zinc-800 dark:border-violet-500':
                                currentChart === 'links',
                        }"
                    >
                        <div class="space-y-2">
                            <IconLink class="h-6 w-6 text-zinc-400" />
                            <div class="text-zinc-800 dark:text-zinc-300">
                                Links created
                            </div>
                            <div
                                class="text-2xl font-semibold text-zinc-800 dark:text-zinc-300"
                            >
                                {{ usage.links.used }}
                            </div>
                        </div>
                        <div>
                            <div class="overflow-hidden rounded-full bg-zinc-200">
                                <div
                                    class="h-1 rounded-full bg-gradient-to-r from-violet-400 to-violet-600"
                                    :style="`width: ${usage.links.percent}%`"
                                />
                            </div>
                            <div
                                class="mt-2 text-zinc-800 dark:text-zinc-300 text-sm"
                            >
                                {{ links }}
                            </div>
                        </div>
                    </div>

                    <div
                        @click="currentChart = 'events'"
                        :class="{
                            'gap-x-4 gap-y-2 bg-white dark:bg-zinc-800 p-6 space-y-4 rounded-lg overflow-hidden cursor-pointer': true,
                            'border border-zinc-200 dark:border-zinc-700':
                                currentChart !== 'events',
                            'border-2 border-zinc-800 dark:border-violet-500':
                                currentChart === 'events',
                        }"
                    >
                        <div class="space-y-2">
                            <IconClick class="h-6 w-6 text-zinc-400" />
                            <div class="text-zinc-800 dark:text-zinc-300">
                                Events tracked
                            </div>
                            <div
                                class="text-2xl font-semibold text-zinc-800 dark:text-zinc-300"
                            >
                                {{ usage.events.used }}
                            </div>
                        </div>
                        <div>
                            <div class="overflow-hidden rounded-full bg-zinc-200">
                                <div
                                    class="h-1 rounded-full bg-gradient-to-r from-violet-400 to-violet-600"
                                    :style="`width: ${usage.events.percent}%`"
                                />
                            </div>
                            <div
                                class="mt-2 text-zinc-800 dark:text-zinc-300 text-sm"
                            >
                                {{ events }}
                            </div>
                        </div>
                    </div>
                </div>

                <div v-show="currentChart === 'links'">
                    <Chart
                        title="Links"
                        :data="chartDataLinks"
                        type="bar"
                        class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-4 bg-white dark:bg-zinc-800 p-4 mb-4"
                    />
                </div>

                <div v-show="currentChart === 'events'">
                    <Chart
                        title="Events"
                        :data="chartDataEvents"
                        type="bar"
                        class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-4 bg-white dark:bg-zinc-800 p-4 mb-4"
                    />
                </div>

                <div
                    class="mx-auto grid grid-cols-1 gap-px bg-zinc-100 dark:bg-zinc-700 sm:grid-cols-3 border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden"
                >
                    <div class="bg-white dark:bg-zinc-800 p-4 space-y-4">
                        <div class="space-y-2">
                            <IconTag class="h-6 w-6 text-zinc-400" />
                            <div class="text-zinc-800 dark:text-zinc-300">Tags</div>
                            <div
                                class="text-2xl font-semibold text-zinc-800 dark:text-zinc-300"
                            >
                                {{ usage.tags.used }}
                            </div>
                        </div>
                        <div>
                            <div class="overflow-hidden rounded-full bg-zinc-200">
                                <div
                                    class="h-1 rounded-full bg-gradient-to-r from-violet-400 to-violet-600"
                                    :style="`width: ${usage.tags.percent}%`"
                                />
                            </div>
                            <div
                                class="mt-2 text-zinc-800 dark:text-zinc-300 text-sm"
                            >
                                {{ tags }}
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-zinc-800 p-4 space-y-4">
                        <div class="space-y-2">
                            <IconWorld class="h-6 w-6 text-zinc-400" />
                            <div class="text-zinc-800 dark:text-zinc-300">
                                Domains
                            </div>
                            <div
                                class="text-2xl font-semibold text-zinc-800 dark:text-zinc-300"
                            >
                                {{ usage.domains.used }}
                            </div>
                        </div>
                        <div>
                            <div class="overflow-hidden rounded-full bg-zinc-200">
                                <div
                                    class="h-1 rounded-full bg-gradient-to-r from-violet-400 to-violet-600"
                                    :style="`width: ${usage.domains.percent}%`"
                                />
                            </div>
                            <div
                                class="mt-2 text-zinc-800 dark:text-zinc-300 text-sm"
                            >
                                {{ domains }}
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-zinc-800 p-4 space-y-4">
                        <div class="space-y-2">
                            <IconUsers class="h-6 w-6 text-zinc-400" />
                            <div class="text-zinc-800 dark:text-zinc-300">
                                Users
                            </div>
                            <div
                                class="text-2xl font-semibold text-zinc-800 dark:text-zinc-300"
                            >
                                {{ usage.users.used }}
                            </div>
                        </div>
                        <div>
                            <div class="overflow-hidden rounded-full bg-zinc-200">
                                <div
                                    class="h-1 rounded-full bg-gradient-to-r from-violet-400 to-violet-600"
                                    :style="`width: ${usage.users.percent}%`"
                                />
                            </div>
                            <div
                                class="mt-2 text-zinc-800 dark:text-zinc-300 text-sm"
                            >
                                {{ users }}
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden p-4"
                >
                    <div class="flex items-center w-full space-x-2">
                        <div class="flex-shrink-0">
                            <IconInfoCircle class="h-5 w-5 text-zinc-400" />
                        </div>
                        <div
                            class="flex w-full justify-between items-center space-x-2"
                        >
                            <div
                                class="text-sm font-medium text-zinc-800 dark:text-white"
                            >
                                For higher limits, upgrade to the
                                {{ usage.plan.next_tier.name }} plan.
                            </div>
                            <div>
                                <a
                                    :href="
                                        usage.billing.has_subscription
                                            ? billingRoutes.portal.url()
                                            : billingRoutes.upgrade.url()
                                    "
                                    class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2"
                                >
                                    Upgrade
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
