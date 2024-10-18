<script setup>
import { usePage, Link } from "@inertiajs/vue3";
import { ref } from "vue";
import { RadioGroup, RadioGroupLabel, RadioGroupOption } from "@headlessui/vue";
import helper from "@/helper";
import Button from "@/Components/Button.vue";

import { IconCheck } from "@tabler/icons-vue";

const project = usePage().props.auth.user.current_project;
const plans = usePage().props.plans;

const frequencies = [
    { value: "annually", label: "Annually" },
    { value: "monthly", label: "Monthly" },
];

const tierStarterMonthly = plans.filter(
    (plan) => plan.internal_id == "starter-monthly"
)[0];

const tierStarterYearly = plans.filter(
    (plan) => plan.internal_id == "starter-yearly"
)[0];

const tierProMonthly = plans.filter(
    (plan) => plan.internal_id == "pro-monthly"
)[0];
const tierProYearly = plans.filter(
    (plan) => plan.internal_id == "pro-yearly"
)[0];

const tierScaleMonthly = plans.filter(
    (plan) => plan.internal_id == "scale-monthly"
)[0];
const tierScaleYearly = plans.filter(
    (plan) => plan.internal_id == "scale-yearly"
)[0];

const tiers = [
    {
        name: "Starter",
        id: "tier-starter",
        btn: "Upgrade to Starter",
        linkMonthly: route(
            "setting.billing.checkout",
            tierStarterMonthly.stripe_id
        ),
        linkAnnually: route(
            "setting.billing.checkout",
            tierStarterYearly.stripe_id
        ),
        price: {
            monthly: "$59",
            annually: "$49",
            annuallySave: "$10",
        },
        features: [
            "Everything In Free, Plus...",
            "Up to 2 Feedback Boards",
            "Changelog Multiple Labels",
            "Changelog Comments & Reactions",
            "Changelog Scheduling",
            "Custom Domain + SSL",
            "Liveboard Multiple Languages",
            "In-App notifications",
            "Integration",
            "API Access",
        ],
        mostPopular: false,
    },
    {
        name: "Pro",
        id: "tier-pro",
        btn: "Upgrade to Pro",
        linkMonthly: route(
            "setting.billing.checkout",
            tierProMonthly.stripe_id
        ),
        linkAnnually: route(
            "setting.billing.checkout",
            tierProYearly.stripe_id
        ),
        price: {
            monthly: "$119",
            annually: "$99",
            annuallySave: "$20",
        },
        features: [
            "Everything in Starter, plus...",
            "5 Feedback Boards",
            "3 Team members (+$20 per additional)",
            "Feedback Moderation tools",
            "Feedback Internal Comments",
            "Custom Feedback Tags",
            "Custom Roadmap Statuses",
            "Custom Changelog Labels",
            "Changelog Email notifications",
            "Sync user data",
            "3 Integrations",
        ],
        mostPopular: true,
    },
    {
        name: "Scale",
        id: "tier-scale",
        btn: "Upgrade to Scale",
        linkMonthly: route(
            "setting.billing.checkout",
            tierScaleMonthly.stripe_id
        ),
        linkAnnually: route(
            "setting.billing.checkout",
            tierScaleYearly.stripe_id
        ),
        price: {
            monthly: "$299",
            annually: "$249",
            annuallySave: "$50",
        },
        features: [
            "Everything in Pro, plus...",
            "Unlimited Feedback Boards",
            "5 Team member (+$20 per additional)",
            "Single Sign-On (SSO)",
            "Changelog Multiple Languages",
            "Custom User Segmentations",

            "High priority support",
            "Private Liveboard",
            "Private Feedback Boards",
            "Unlimited Integrations",
        ],
        mostPopular: false,
    },
    {
        name: "Custom",
        id: "tier-custom",
        btn: "Contact US",
        linkMonthly: "",
        linkAnnually: "",
        price: {
            monthly: "Let's talk",
            annually: "Let's talk",
            annuallySave: "",
        },
        features: [
            "Account manager",
            "Onboarding and installation assistance",
            "Unlimited Team members",
            "Remove branding",
            "Email whitelabeling (SMTP)",
            "Pay by invoice",
            "Audit logs",
        ],
        mostPopular: false,
    },
];

const frequency = ref(frequencies[0]);
</script>

<template>
    <div class="flex flex-col justify-center">
        <div class="flex justify-center mb-4">
            <RadioGroup
                v-model="frequency"
                class="grid grid-cols-2 gap-x-1 rounded-full bg-black/5 dark:bg-white/5 p-1 text-center font-medium leading-5"
            >
                <RadioGroupLabel class="sr-only"
                    >Payment frequency</RadioGroupLabel
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
                            checked
                                ? 'bg-gray-800 dark:bg-violet-500 text-white'
                                : 'text-gray-600 hover:text-black dark:text-gray-400 hover:dark:text-white',
                            'cursor-pointer rounded-full px-4 py-2',
                        ]"
                    >
                        <span>{{ option.label }}</span>
                    </div>
                </RadioGroupOption>
            </RadioGroup>
        </div>
        <div
            class="relative mx-auto font-medium text-gray-600 dark:text-gray-400 pay-annually"
        >
            Get 2 months for free by paying annually 🎉
        </div>
    </div>
    <div
        class="isolate mx-auto mt-8 grid max-w-md grid-cols-1 gap-8 lg:mx-0 lg:max-w-none lg:grid-cols-4"
    >
        <div
            v-for="tier in tiers"
            :key="tier.id"
            :class="[
                tier.mostPopular
                    ? 'bg-white/5 ring-2 ring-violet-500'
                    : 'ring-1 ring-gray-200 dark:ring-white/10',
                'rounded-3xl py-8 px-6',
            ]"
        >
            <div class="flex items-center justify-between gap-x-4">
                <h3
                    :id="tier.id"
                    class="text-lg font-semibold leading-8 text-dark dark:text-white"
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

            <p class="mt-6 flex items-baseline gap-x-1">
                <span
                    class="text-4xl font-bold tracking-tight text-gray-800 dark:text-white"
                >
                    {{ tier.price[frequency.value] }}
                </span>
                <span
                    class="text-sm font-semibold leading-6 text-gray-600 dark:text-gray-300"
                >
                    {{ tier.id == "tier-custom" ? "" : "/month" }}
                </span>
            </p>
            <div class="my-4 spacep-y-2">
                <div
                    v-if="frequency.value == 'annually'"
                    class="text-sm dark:text-gray-300 font-medium"
                >
                    Billed <b>annually</b>
                </div>
                <div class="text-sm dark:text-gray-400 font-medium">
                    <div
                        v-if="
                            frequency.value == 'annually' &&
                            tier.id != 'tier-custom'
                        "
                    >
                        Save <b>{{ tier.price.annuallySave }}</b> each month
                    </div>
                </div>
            </div>

            <a
                v-if="tier.id != 'tier-custom'"
                :href="
                    frequency.value == 'monthly'
                        ? tier.linkMonthly
                        : tier.linkAnnually
                "
                :class="[
                    tier.mostPopular
                        ? 'bg-violet-600 text-white shadow-sm hover:bg-violet-700 focus-visible:outline-violet-700'
                        : 'bg-gray-800 dark:bg-white/10 text-white hover:bg-violet-600 hover:dark:bg-violet-600 focus-visible:outline-white',
                    'cursor-pointer mt-6 block uppercase rounded-md py-2 px-3 text-center text-sm font-semibold leading-6 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2',
                ]"
            >
                {{ tier.btn }}
            </a>
            <div v-else>
                <div
                    @click.prevent="helper.openCrispChat()"
                    class="bg-gray-800 dark:bg-white/10 hover:bg-violet-600 hover:dark:bg-violet-600 text-white focus-visible:outline-white cursor-pointer mt-6 block uppercase rounded-md py-2 px-3 text-center text-sm font-semibold leading-6 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2"
                >
                    {{ tier.btn }}
                </div>
            </div>
            <ul
                role="list"
                class="mt-4 xl:mt-6 space-y-1.5 text-sm leading-6 text-gray-800 dark:text-gray-300"
            >
                <li
                    v-for="feature in tier.features"
                    :key="feature"
                    class="flex gap-x-2"
                >
                    <IconCheck
                        class="h-6 w-5 flex-none text-green-500 dark:text-white"
                        aria-hidden="true"
                    />
                    <div class="capitalize">
                        {{ feature }}
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <!-- <div
        class="ring-1 ring-gray-200 dark:ring-white/10 rounded-3xl py-8 px-6 mt-8"
    >
        <div class="flex items-center justify-between space-x-4">
            <div class="flex items-center space-x-4 flex-none">
                <span
                    class="text-2xl font-bold tracking-tight text-gray-800 dark:text-white"
                    >Free</span
                >

                <div class="flex items-center space-x-1">
                    <div
                        class="text-4xl font-bold tracking-tight text-gray-800 dark:text-white"
                    >
                        $0
                    </div>
                    <div
                        class="text-sm font-semibold text-gray-600 dark:text-gray-300"
                    >
                        /forever
                    </div>
                </div>
            </div>
            <div class="text-sm text-gray-500">
                This plan includes: 1 Feedback Board, Product Roadmap,
                Changelog, 1 Team member, In-App Widget and Live board.
            </div>
            <div class="flex-none">
                <button
                    v-if="project.plan.internal_id == 'free'"
                    class="btn btn-secondary"
                >
                    Current Plan
                </button>
                <Button
                    v-else
                    class="btn-secondary"
                    :href="route('setting.billing.swap-free-plan')"
                >
                    Downgrade to Free Plan
                </Button>
            </div>
        </div>
    </div> -->
</template>

<style lang="scss" scoped>
.pay-annually::after {
    content: "";
    display: block;
    height: 60px;
    width: 50px;
    background-image: url(/images/admin/upgrade/arrow-plan.svg);
    background-position: center center;
    background-size: 100% auto;
    background-repeat: no-repeat;
    position: absolute;
    left: 6px;
    bottom: 28px;
    transform: rotate(-100deg) scale(1, -1);
}
</style>
