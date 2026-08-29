<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    status: number;
}>();

const title = computed(() => {
    return ({
        503: 'Service Unavailable',
        500: 'Server Error',
        404: 'Page Not Found',
        403: 'Forbidden',
    } as Record<number, string>)[props.status] ?? 'Error';
});

const description = computed(() => {
    return ({
        503: 'Sorry, we are doing some maintenance. Please check back soon.',
        500: 'Whoops, something went wrong on our servers.',
        404: 'Sorry, the page you are looking for could not be found.',
        403: 'Sorry, you are forbidden from accessing this page.',
    } as Record<number, string>)[props.status] ?? 'An error occurred.';
});
</script>

<template>
    <main class="grid min-h-full place-items-center bg-background px-6 py-24 sm:py-48 lg:px-8">
        <div class="text-center">
            <p class="text-4xl font-semibold text-primary">
                {{ props.status }}
            </p>
            <h1 class="mt-4 text-3xl font-bold tracking-tight text-foreground sm:text-5xl">
                {{ title }}
            </h1>
            <p class="mt-6 text-base leading-7 text-muted-foreground">
                {{ description }}
            </p>
        </div>
    </main>
</template>
