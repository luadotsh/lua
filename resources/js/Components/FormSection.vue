<script setup>
import { computed, useSlots } from "vue";
import SectionTitle from "./SectionTitle.vue";

defineEmits(["submitted"]);

const hasActions = computed(() => !!useSlots().actions);
</script>

<template>
    <div class="">
        <form @submit.prevent="$emit('submitted')">
            <div class="px-4 py-5 sm:p-6">
                <SectionTitle>
                    <template #title>
                        <div class="text-2xl font-semibold text-zinc-900">
                            <slot name="title" />
                        </div>
                    </template>
                    <template #titleActions>
                        <slot name="titleActions" />
                    </template>
                    <template #description>
                        <div class="text-base text-zinc-500">
                            <slot name="description" />
                        </div>
                    </template>
                </SectionTitle>
                <div class="grid grid-cols-6 gap-x-6 gap-y-4 pt-4">
                    <slot name="form" />
                </div>
            </div>

            <div
                v-if="hasActions"
                class="flex items-center justify-start py-2 text-right px-4 sm:px-6"
            >
                <slot name="actions" />
            </div>
        </form>
    </div>
</template>
