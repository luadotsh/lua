<script setup>
import { computed } from "vue";

const { size } = defineProps({
    user: {
        type: Object,
        required: true,
    },

    size: {
        type: String,
        default: "md",
    },
});

const userSize = computed(() => {
    switch (size) {
        case "2xl":
            return "h-14 w-14";

        case "xl":
            return "h-12 w-12";

        case "lg":
            return "h-10 w-10";

        case "md":
            return "h-8 w-8";

        case "sm":
            return "h-5 w-5";

        case "xs":
            return "h-4 w-4";
    }
});
</script>

<template>
    <img
        :src="user.photo_url"
        :class="userSize"
        :alt="user.name"
        class="rounded-full"
    />
</template>
