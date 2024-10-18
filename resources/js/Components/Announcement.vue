<script setup>
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";

const announcement = computed(() => usePage().props.announcement);

defineProps({
    class: {
        type: String,
        default: "",
    },
});
</script>

<template>
    <div
        v-if="announcement"
        class="bg-red-500 flex items-center justify-center p-2"
        :class="class"
    >
        <h1 class="text-sm font-normal text-white">
            {{ announcement }}
        </h1>
    </div>
</template>
