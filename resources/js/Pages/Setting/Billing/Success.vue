<script setup>
import { onMounted } from "vue";
import { Head, router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/Master.vue";

import { PhSpinnerGap } from "@phosphor-icons/vue";

onMounted(() => {
    setTimeout(() => {
        router.visit(route("setting.billing.index"));
    }, 8000);
});
</script>

<template>
    <Head title="Processing Payment" />
    <AppLayout>
        <div class="flex min-h-full flex-1 flex-col justify-center">
            <div class="max-w-3xl mx-auto">
                <div class="flex items-center space-x-2">
                    <PhSpinnerGap
                        class="h-6 w-6 text-zinc-800 dark:text-zinc-300 animate-spin"
                    />
                    <div
                        class="font-medium text-zinc-800 dark:text-zinc-300 text-lg"
                    >
                        Please wait while we process your payment.
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
