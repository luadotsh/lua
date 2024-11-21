<script setup>
defineProps({
    tag: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div
        :class="`inline-flex text-${tag.color}-500 ring-1 ring-inset ring-${tag.color}-300 ring-opacity-40 text-xs truncate font-medium px-2 py-1 rounded`"
    >
        {{ tag.name }}
    </div>
</template>
