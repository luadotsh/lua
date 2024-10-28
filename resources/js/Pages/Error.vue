<script setup>
import { computed } from "vue";

import { useDarkTheme } from "@/theme";
const { isDarkTheme } = useDarkTheme();

const props = defineProps({ status: Number });

const title = computed(() => {
    return {
        503: "Service Unavailable",
        500: "Server Error",
        404: "Page Not Found",
        403: "Forbidden",
    }[props.status];
});

const description = computed(() => {
    return {
        503: "Sorry, we are doing some maintenance. Please check back soon.",
        500: "Whoops, something went wrong on our servers.",
        404: "Sorry, the page you are looking for could not be found.",
        403: "Sorry, you are forbidden from accessing this page.",
    }[props.status];
});
</script>

<template>
    <main
        class="grid min-h-full place-items-center bg-white dark:bg-zinc-900 px-6 py-24 sm:py-48 lg:px-8 border-b border-zinc-100 dark:border-zinc-700"
    >
        <div class="text-center">
            <p class="text-4xl font-semibold text-violet-600">
                {{ props.status }}
            </p>
            <h1
                class="mt-4 text-3xl font-bold tracking-tight text-zinc-800 dark:text-zinc-300 sm:text-5xl"
            >
                {{ title }}
            </h1>
            <p
                class="mt-6 text-base leading-7 text-zinc-600 dark:text-zinc-400"
            >
                {{ description }}
            </p>
        </div>
    </main>
</template>
