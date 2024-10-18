<script setup>
import SectionTitle from "./SectionTitle.vue";
</script>

<template>
    <div class="px-4 py-4 sm:px-6 sm:py-6">
        <SectionTitle>
            <template #title>
                <div class="text-black">
                    <slot name="title" />
                </div>
            </template>
            <template #description>
                <div class="text-sm font-light text-zinc-400">
                    <slot name="description" />
                </div>
            </template>
        </SectionTitle>

        <div class="mt-5 md:mt-0 md:col-span-2">
            <div class="pt-4 text-black text-sm">
                <slot name="content" />
            </div>
        </div>

        <div class="mt-5">
            <slot name="action" />
        </div>
    </div>
</template>
