<script setup>
import { PhInfo } from "@phosphor-icons/vue";

defineProps({
    value: {
        type: String,
        required: true,
    },
    for: {
        type: String,
        default: null,
    },
    required: {
        type: Boolean,
        default: false,
    },
    tooltip: {
        type: String,
        default: null,
    },
});
</script>

<template>
    <label class="label" :for="for">
        <span>{{ value }}</span>
        <span v-if="required" class="text-red-500 ml-1">*</span>
        <PhInfo
            v-if="tooltip"
            class="h-4 w-4 ml-1"
            v-tooltip="{
                content: tooltip,
            }"
        />
    </label>
</template>
