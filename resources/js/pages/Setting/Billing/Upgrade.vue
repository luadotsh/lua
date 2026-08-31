<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { IconCircleCheck } from '@tabler/icons-vue';
import { ref } from 'vue';

import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { cn } from '@/lib/utils';
import * as billingRoutes from '@/routes/setting/billing';
import type { BillingFrequency, Plan } from '@/types';

const plans = usePage().props.plans as Plan[];

const getPlanLink = (id: string) => {
    const plan = plans.find((plan) => plan.internal_id == id)!;
    return billingRoutes.checkout.url(plan.id);
};

const frequencies: {
    value: BillingFrequency;
    label: string;
    priceSuffix: string;
}[] = [
    { value: 'monthly', label: 'Monthly', priceSuffix: 'per month' },
    {
        value: 'annually',
        label: 'Annually',
        priceSuffix: 'per month',
    },
];

const tiers = [
    {
        name: 'Starter',
        id: 'tier-starter',
        price: { monthly: '$19', annually: '$15' },
        features: [
            '1,000 new links/mo',
            '50K tracked clicks/mo',
            '10 custom domains',
            '2 team members',
            'Real-time events stream',
            '1-year data retention',
            'Priority support',
        ],
        mostPopular: false,
        btn: 'Get started with Starter',
        linkMonthly: getPlanLink('starter-monthly'),
        linkAnnually: getPlanLink('starter-yearly'),
    },
    {
        name: 'Pro',
        id: 'tier-pro',
        price: { monthly: '$49', annually: '$40' },
        features: [
            '5,000 new links/mo',
            '150K tracked clicks/mo',
            '50 custom domains',
            '5 team members',
            'Real-time events stream',
            '1-year data retention',
            'Priority support',
        ],
        mostPopular: true,
        btn: 'Get started with Pro',
        linkMonthly: getPlanLink('pro-monthly'),
        linkAnnually: getPlanLink('pro-yearly'),
    },
    {
        name: 'Growth',
        id: 'tier-growth',
        price: { monthly: '$149', annually: '$124' },
        features: [
            '20,000 new links/mo',
            '500K tracked clicks/mo',
            '200 custom domains',
            '10 team members',
            'Real-time events stream',
            '1-year data retention',
            'Priority support',
        ],
        mostPopular: false,
        btn: 'Get started with Growth',
        linkMonthly: getPlanLink('growth-monthly'),
        linkAnnually: getPlanLink('growth-yearly'),
    },
    {
        name: 'Scale',
        id: 'tier-scale',
        price: { monthly: '$349', annually: '$290' },
        features: [
            '100,000 new links/mo',
            '2M tracked clicks/mo',
            '500 custom domains',
            '20 team members',
            'Real-time events stream',
            '3-year data retention',
            'Priority support',
        ],
        mostPopular: false,
        btn: 'Get started with Scale',
        linkMonthly: getPlanLink('scale-monthly'),
        linkAnnually: getPlanLink('scale-yearly'),
    },
];

const frequency = ref(frequencies[0]);
</script>

<template>
    <Head title="Upgrade" />

    <AppLayout>
        <SettingsLayout
            title="Choose your plan"
            description="Find a plan that fits your needs and start tracking your links."
            wide
        >
            <div class="flex flex-col gap-8">
                <fieldset
                    aria-label="Payment frequency"
                    class="flex justify-center"
                >
                    <div
                        class="grid grid-cols-2 gap-1 rounded-full bg-muted p-1 text-center text-xs font-semibold"
                    >
                        <button
                            v-for="option in frequencies"
                            :key="option.value"
                            type="button"
                            :aria-pressed="frequency.value === option.value"
                            :class="
                                cn(
                                    'cursor-pointer rounded-full px-3 py-1 transition-colors',
                                    frequency.value === option.value
                                        ? 'bg-violet-500 text-white'
                                        : 'text-muted-foreground hover:text-foreground',
                                )
                            "
                            @click="frequency = option"
                        >
                            {{ option.label }}
                        </button>
                    </div>
                </fieldset>

                <div
                    class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4"
                >
                    <div
                        v-for="tier in tiers"
                        :key="tier.id"
                        :class="
                            cn(
                                'flex flex-col rounded-lg border bg-card p-5',
                                tier.mostPopular
                                    ? 'border-violet-500 ring-1 ring-violet-500'
                                    : 'border-border',
                            )
                        "
                    >
                        <div class="flex items-center justify-between gap-2">
                            <h3
                                :id="tier.id"
                                class="text-lg font-semibold text-foreground"
                            >
                                {{ tier.name }}
                            </h3>
                            <span
                                v-if="tier.mostPopular"
                                class="rounded-full bg-violet-500 px-2 py-0.5 text-xs font-semibold whitespace-nowrap text-white"
                            >
                                Most popular
                            </span>
                        </div>

                        <div class="mt-5 flex items-baseline gap-1.5">
                            <span
                                class="text-4xl font-semibold tracking-tight text-foreground tabular-nums"
                            >
                                {{ tier.price[frequency.value] }}
                            </span>
                            <span class="text-sm text-muted-foreground">
                                {{ frequency.priceSuffix }}
                            </span>
                        </div>

                        <!-- Kept in flow rather than v-if so the cards stay aligned
                             when the toggle flips. -->
                        <p class="mt-1 h-5 text-sm text-muted-foreground">
                            {{
                                frequency.value === 'annually'
                                    ? 'Billed annually'
                                    : ''
                            }}
                        </p>

                        <Button
                            as="a"
                            class="mt-5 w-full"
                            :variant="tier.mostPopular ? 'default' : 'outline'"
                            :href="
                                frequency.value === 'monthly'
                                    ? tier.linkMonthly
                                    : tier.linkAnnually
                            "
                            :aria-describedby="tier.id"
                        >
                            {{ tier.btn }}
                        </Button>

                        <ul
                            role="list"
                            class="mt-6 flex flex-col gap-3 text-sm text-muted-foreground"
                        >
                            <li
                                v-for="feature in tier.features"
                                :key="feature"
                                class="flex gap-2"
                            >
                                <IconCircleCheck
                                    class="mt-0.5 size-4 shrink-0 text-violet-500"
                                />
                                {{ feature }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
