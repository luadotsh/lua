<script setup lang="ts">
import SettingsNav from '@/components/SettingsNav.vue';
</script>

<template>
    <div class="mx-auto max-w-4xl px-4 py-6">
        <SettingsNav class="mb-8" />

        <section class="space-y-12">
            <slot />
        </section>
    </div>
</template>
