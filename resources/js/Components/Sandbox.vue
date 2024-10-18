<script setup>
const sandbox = import.meta.env.VITE_SANDBOX ?? false;

defineProps({
    class: {
        type: String,
        default: "",
    },
});
</script>

<template>
    <div
        v-if="sandbox"
        class="bg-red-500 flex items-center justify-center p-2"
        :class="class"
    >
        <h1 class="text-sm font-normal text-white">
            Your are in the sandbox environment.
        </h1>
    </div>
</template>
