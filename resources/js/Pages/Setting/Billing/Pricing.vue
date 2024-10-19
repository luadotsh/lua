<script setup>
import { ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import { RadioGroup, RadioGroupOption } from "@headlessui/vue";
import { PhCheck } from "@phosphor-icons/vue";

const plans = usePage().props.plans;

const getPlanLink = (id) => {
    const plan = plans.filter((plan) => plan.internal_id == id)[0];
    return route("setting.billing.checkout", plan.stripe_id);
};

const frequencies = [
    { value: "annually", label: "Annually", priceSuffix: "/month" },
    { value: "monthly", label: "Monthly", priceSuffix: "/month" },
];
const tiers = [
    {
        name: "Starter",
        id: "tier-starter",
        price: { monthly: "$19", annually: "$15" },
        description: "The essentials to provide your best work for clients.",
        features: [
            "1,000 new links per month",
            "50K tracked clicks per month",
            "Real-time events stream",
            "Unlimited custom domains",
            "Unlimited team members",
            "1-year analytics retention",
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
        description: "A plan that scales with your rapidly growing business.",
        features: [
            "5,000 new links per month",
            "150K tracked clicks per month",
            "Real-time events stream",
            "Unlimited custom domains",
            "Unlimited team members",
            "1-year analytics retention",
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
        description: "Dedicated support and infrastructure for your company.",
        features: [
            "20,000 new links per month",
            "500K tracked clicks per month",
            "Real-time events stream",
            "Unlimited custom domains",
            "Unlimited team members",
            "1-year analytics retention",
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
        description: "Dedicated support and infrastructure for your company.",
        features: [
            "100,000 new links per month",
            "2M tracked clicks per month",
            "Real-time events stream",
            "Unlimited custom domains",
            "Unlimited team members",
            "3-year analytics retention",
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
    <div>
        <div class="mx-auto max-w-4xl text-center">
            <p
                class="mt-2 text-4xl font-bold tracking-tight text-white sm:text-5xl"
            >
                Pricing plans for teams of&nbsp;all&nbsp;sizes
            </p>
        </div>
        <div class="mt-16 flex justify-center">
            <fieldset aria-label="Payment frequency">
                <RadioGroup
                    v-model="frequency"
                    class="grid grid-cols-2 gap-x-1 rounded-full bg-white/5 p-1 text-center text-xs font-semibold leading-5 text-white"
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
                                checked ? 'bg-indigo-500' : '',
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
            class="isolate mx-auto mt-10 grid max-w-md grid-cols-1 gap-8 lg:mx-0 lg:max-w-none lg:grid-cols-4"
        >
            <div
                v-for="tier in tiers"
                :key="tier.id"
                :class="[
                    tier.mostPopular
                        ? 'bg-white/5 ring-2 ring-indigo-500'
                        : 'ring-1 ring-white/10',
                    'rounded-3xl p-8 xl:p-10',
                ]"
            >
                <div class="flex items-center justify-between gap-x-4">
                    <h3
                        :id="tier.id"
                        class="text-lg font-semibold leading-8 text-white"
                    >
                        {{ tier.name }}
                    </h3>
                    <p
                        v-if="tier.mostPopular"
                        class="rounded-full bg-indigo-500 px-2.5 py-1 text-xs font-semibold leading-5 text-white"
                    >
                        Most popular
                    </p>
                </div>
                <p class="mt-4 text-sm leading-6 text-gray-300">
                    {{ tier.description }}
                </p>
                <p class="mt-6 flex items-baseline gap-x-1">
                    <span
                        class="text-4xl font-bold tracking-tight text-white"
                        >{{ tier.price[frequency.value] }}</span
                    >
                    <span
                        class="text-sm font-semibold leading-6 text-gray-300"
                        >{{ frequency.priceSuffix }}</span
                    >
                </p>
                <div class="my-4 spacep-y-2">
                    <div
                        v-if="frequency.value == 'annually'"
                        class="text-sm dark:text-gray-300 font-medium"
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
                            ? 'bg-indigo-500 text-white shadow-sm hover:bg-indigo-400 focus-visible:outline-indigo-500'
                            : 'bg-white/10 text-white hover:bg-white/20 focus-visible:outline-white',
                        'mt-6 block rounded-md px-3 py-2 text-center text-sm font-semibold leading-6 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2',
                    ]"
                >
                    {{ tier.btn }}
                </a>
                <ul
                    role="list"
                    class="mt-8 space-y-3 text-sm leading-6 text-gray-300 xl:mt-10"
                >
                    <li
                        v-for="feature in tier.features"
                        :key="feature"
                        class="flex gap-x-3"
                    >
                        <PhCheck
                            class="h-6 w-5 flex-none text-white"
                            aria-hidden="true"
                        />
                        {{ feature }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
