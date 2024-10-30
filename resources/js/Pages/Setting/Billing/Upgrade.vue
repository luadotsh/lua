<script setup>
import { ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import { RadioGroup, RadioGroupOption } from "@headlessui/vue";
import { PhCheckCircle } from "@phosphor-icons/vue";

import AppLayout from "@/Layouts/Master.vue";

const plans = usePage().props.plans;

const getPlanLink = (id) => {
    const plan = plans.filter((plan) => plan.internal_id == id)[0];
    return route("setting.billing.checkout", plan.id);
};

const frequencies = [
    { value: "monthly", label: "Monthly", priceSuffix: "per month" },
    {
        value: "annually",
        label: "Annually",
        priceSuffix: "per month",
    },
];
const tiers = [
    {
        name: "Starter",
        id: "tier-starter",
        price: { monthly: "$19", annually: "$15" },
        features: [
            "1,000 new links/mo",
            "50K tracked clicks/mo",
            "10 custom domains",
            "2 team members",
            "Real-time events stream",
            "1-year data retention",
            "Priority support",
        ],
        mostPopular: false,
        btn: "Get started with Starter",
        linkMonthly: getPlanLink("starter-monthly"),
        linkAnnually: getPlanLink("starter-yearly"),
    },
    {
        name: "Pro",
        id: "tier-pro",
        price: { monthly: "$49", annually: "$40" },
        features: [
            "5,000 new links/mo",
            "150K tracked clicks/mo",
            "50 custom domains",
            "5 team members",
            "Real-time events stream",
            "1-year data retention",
            "Priority support",
        ],
        mostPopular: true,
        btn: "Get started with Pro",
        linkMonthly: getPlanLink("pro-monthly"),
        linkAnnually: getPlanLink("pro-yearly"),
    },
    {
        name: "Growth",
        id: "tier-growth",
        price: { monthly: "$149", annually: "$124" },
        features: [
            "20,000 new links/mo",
            "500K tracked clicks/mo",
            "200 custom domains",
            "10 team members",
            "Real-time events stream",
            "1-year data retention",
            "Priority support",
        ],
        mostPopular: false,
        btn: "Get started with Growth",
        linkMonthly: getPlanLink("growth-monthly"),
        linkAnnually: getPlanLink("growth-yearly"),
    },
    {
        name: "Scale",
        id: "tier-scale",
        price: { monthly: "$349", annually: "$290" },
        features: [
            "100,000 new links/mo",
            "2M tracked clicks/mo",
            "500 custom domains",
            "20 team members",
            "Real-time events stream",
            "3-year data retention",
            "Priority support",
        ],
        mostPopular: false,
        btn: "Get started with Scale",
        linkMonthly: getPlanLink("scale-monthly"),
        linkAnnually: getPlanLink("scale-yearly"),
    },
];

const frequency = ref(frequencies[0]);
</script>

<template>
    <AppLayout>
        <div class="py-8">
            <div class="mx-auto max-w-4xl text-center">
                <div
                    class="text-2xl font-semibold text-zinc-800 dark:text-zinc-100"
                >
                    Choose your plan
                </div>
                <div class="text-zinc-600 dark:text-zinc-400">
                    Find a plan that fits your needs and start tracking your
                    links
                </div>
            </div>
            <div class="mt-12 flex justify-center">
                <fieldset aria-label="Payment frequency">
                    <RadioGroup
                        v-model="frequency"
                        class="grid grid-cols-2 gap-x-1 rounded-full bg-zinc-100 dark:bg-white/5 p-1 text-center text-xs font-semibold leading-5 dark:text-white"
                    >
                        <RadioGroupOption
                            as="template"
                            v-for="option in frequencies"
                            :key="option.value"
                            :value="option"
                            v-slot="{ checked }"
                        >
                            <div
                                :class="[
                                    checked ? 'bg-violet-500 text-white' : '',
                                    'cursor-pointer rounded-full px-2.5 py-1',
                                ]"
                            >
                                {{ option.label }}
                            </div>
                        </RadioGroupOption>
                    </RadioGroup>
                </fieldset>
            </div>
            <div
                class="isolate mx-auto mt-10 grid max-w-md grid-cols-1 gap-4 xl:gap-8 lg:mx-0 lg:max-w-none lg:grid-cols-4"
            >
                <div
                    v-for="tier in tiers"
                    :key="tier.id"
                    :class="[
                        tier.mostPopular
                            ? 'bg-violet-800/5 dark:bg-white/5 ring-2 ring-violet-500'
                            : 'ring-1 ring-zinc-200 dark:ring-white/10',
                        'rounded-lg p-4 xl:p-6',
                    ]"
                >
                    <div class="flex items-center justify-between gap-x-4">
                        <h3
                            :id="tier.id"
                            class="text-lg font-semibold leading-8 text-zinc-800 dark:text-white"
                        >
                            {{ tier.name }}
                        </h3>
                        <p
                            v-if="tier.mostPopular"
                            class="rounded-full bg-violet-500 px-2.5 py-1 text-xs font-semibold leading-5 text-white"
                        >
                            Most popular
                        </p>
                    </div>

                    <div class="mt-6 flex items-baseline gap-x-1">
                        <div
                            class="text-4xl font-semibold tracking-tight dark:text-white"
                        >
                            {{ tier.price[frequency.value] }}
                        </div>
                        <div
                            class="text-sm font-medium text-zinc-800 dark:text-zinc-300"
                        >
                            {{ frequency.priceSuffix }}
                        </div>
                    </div>
                    <div class="my-4 spacep-y-2">
                        <div
                            v-if="frequency.value == 'annually'"
                            class="text-sm text-zinc-800 dark:text-zinc-300 font-medium"
                        >
                            Billed annually
                        </div>
                    </div>
                    <a
                        :href="
                            frequency.value == 'monthly'
                                ? tier.linkMonthly
                                : tier.linkAnnually
                        "
                        :aria-describedby="tier.id"
                        :class="[
                            tier.mostPopular
                                ? 'bg-violet-500 text-white shadow-sm hover:bg-violet-400 focus-visible:outline-violet-500'
                                : 'bg-zinc-800 dark:bg-white/10 text-white hover:bg-zinc-700 dark:hover:bg-white/20 focus-visible:outline-white',
                            'mt-6 block rounded-md px-3 py-2 text-center text-sm font-semibold leading-6 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2',
                        ]"
                    >
                        {{ tier.btn }}
                    </a>
                    <ul
                        role="list"
                        class="mt-8 space-y-3 text-sm leading-6 text-zinc-800 dark:text-zinc-300 xl:mt-10"
                    >
                        <li
                            v-for="feature in tier.features"
                            :key="feature"
                            class="flex gap-x-3"
                        >
                            <PhCheckCircle
                                class="h-6 w-5 flex-none text-violet-500"
                            />
                            {{ feature }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
