<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

defineProps<{
    title: string;
    description?: string;
    pageTitle?: string;
}>();
</script>

<template>
    <div
        class="flex min-h-svh flex-col items-center justify-center bg-background p-6 md:p-10"
    >
        <Head v-if="pageTitle" :title="pageTitle" />

        <div class="w-full max-w-md space-y-8">
            <div class="flex flex-col items-center gap-4 text-center">
                <img
                    src="/images/lua/full-color.svg"
                    alt="Lua"
                    class="h-8 dark:hidden"
                />
                <img
                    src="/images/lua/full-white.svg"
                    alt="Lua"
                    class="hidden h-8 dark:block"
                />
                <div class="space-y-2">
                    <h1
                        class="text-xl font-semibold tracking-tight text-foreground"
                    >
                        {{ title }}
                    </h1>
                    <p v-if="description" class="text-sm text-muted-foreground">
                        {{ description }}
                    </p>
                </div>
            </div>

            <div
                class="space-y-6 rounded-xl border border-border bg-card p-6 shadow-2xs"
            >
                <slot />
            </div>
        </div>
    </div>
</template>
