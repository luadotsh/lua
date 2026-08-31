<script setup lang="ts">
import RangePicker from '@/components/RangePicker.vue';

interface Range {
    start: string;
    end: string;
    timezone: string;
    group: string;
}

const props = defineProps<{
    range: Range;
}>();

defineEmits<{
    'update:range': [value: Range];
}>();
</script>

<template>
    <RangePicker
        :range="props.range"
        @update:range="$emit('update:range', $event)"
        placement="bottom-end"
    />
</template>
