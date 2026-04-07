<script setup lang="ts">
import AuthLayout from '@/layouts/auth/AuthCardLayout.vue';

withDefaults(defineProps<{
    title?: string;
    description?: string;
    showTerms?: boolean;
}>(), {
    showTerms: false,
});
</script>

<template>
    <AuthLayout :title="title" :description="description" :show-terms="showTerms">
        <slot />
    </AuthLayout>
</template>
