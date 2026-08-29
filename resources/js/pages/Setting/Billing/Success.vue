<script setup lang="ts">
import { onMounted } from "vue";
import { Head, router } from "@inertiajs/vue3";
import AppLayout from "@/layouts/AppLayout.vue";
import SettingsLayout from "@/layouts/settings/Layout.vue";
import { IconLoader2 } from "@tabler/icons-vue";
import * as billingRoutes from "@/routes/setting/billing";

onMounted(() => {
    setTimeout(() => {
        router.visit(billingRoutes.index.url());
    }, 8000);
});
</script>

<template>
    <Head title="Processing Payment" />
    <AppLayout>
        <SettingsLayout>
            <div class="flex min-h-full flex-1 flex-col justify-center">
                <div class="max-w-3xl mx-auto">
                    <div class="flex items-center space-x-2">
                        <IconLoader2
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
        </SettingsLayout>
    </AppLayout>
</template>
