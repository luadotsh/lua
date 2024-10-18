<script setup>
import { ref, computed } from "vue";
import { Head, usePage, Link, useForm } from "@inertiajs/vue3";

import AppLayout from "@/Layouts/Master.vue";
import Button from "@/Components/Button.vue";
import SectionBorder from "@/Components/SectionBorder.vue";
import Pricing from "./Pricing.vue";

const project = computed(() => usePage().props.auth.user.current_project);
const features = computed(() => project.value.features);

const { has_subscription } = usePage().props.billing;
</script>

<template>
    <Head title="Pricing - Changelogfy" />
    <ModalAddSeat ref="modalAddSeat" :project="project" />
    <ModalReduceSeat ref="modalReduceSeat" :project="project" />

    <AppLayout :full-width="has_subscription ? false : true">
        <template #header>
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="page-title">
                        {{ has_subscription ? "Billing" : "Pricing" }}
                    </h1>
                    <div v-if="trial_its_expired" class="page-description">
                        Your trial has expired, please subscribe to continue
                        using the Changelogfy.com
                    </div>
                </div>
            </div>
        </template>

        <div v-if="has_subscription" class="pb-12">
            <div class="flex flex-col space-y-4">
                <div class="px-4 sm:px-0">
                    <p
                        class="text-sm text-black dark:text-gray-300 font-medium"
                    >
                        You're on the <b>{{ project.plan.name }}</b> plan.
                    </p>
                </div>
                <div class="px-4 sm:px-0 flex items-center space-x-2">
                    <Button
                        class="btn-primary"
                        :href="route('setting.billing.portal')"
                    >
                        Change Plan
                    </Button>

                    <Button
                        class="btn-secondary"
                        :href="route('setting.billing.portal')"
                    >
                        Cancel Plan
                    </Button>
                </div>
            </div>

            <SectionBorder />

            <div class="flex flex-col space-y-4">
                <div class="px-4 sm:px-0">
                    <p
                        class="text-sm text-black dark:text-gray-300 font-medium"
                    >
                        Update your payment method, billing details, or download
                        invoices.
                    </p>
                </div>
                <div class="px-4 sm:px-0 flex items-center space-x-2">
                    <Button
                        class="btn-secondary"
                        :href="route('setting.billing.portal')"
                    >
                        Manage Billing
                    </Button>
                </div>
            </div>
        </div>
        <div v-else class="max-w-7xl mx-auto py-8">
            <Pricing />
        </div>
    </AppLayout>
</template>
