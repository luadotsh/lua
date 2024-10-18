<script setup>
import { onMounted } from "vue";
import { Head, router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/Master.vue";

import { IconLoader } from "@tabler/icons-vue";

onMounted(() => {
    setTimeout(() => {
        router.visit(route("setting.billing.index"));
    }, 8000);
});
</script>

<template>
    <Head title="Pricing" />
    <AppLayout :full-width="true">
        <template #header>
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="page-title">Billing</h1>
                    <div class="page-description">
                        We are processing your payment.
                    </div>
                </div>
            </div>
        </template>

        <div class="flex min-h-full flex-1 flex-col justify-center">
            <div class="max-w-3xl mx-auto">
                <div class="flex items-center space-x-2">
                    <IconLoader class="h-6 w-6 text-gray-500 animate-spin" />
                    <div class="font-medium text-black text-lg">
                        Please wait while we process your payment.
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
