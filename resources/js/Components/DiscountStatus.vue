<script setup>
defineProps({
    discount: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div v-if="discount.status === 'Active'" class="badge badge-green">
        Active
    </div>
    <div v-else-if="discount.status === 'Scheduled'" class="badge badge-orange">
        Scheduled
    </div>
    <div v-else-if="discount.status === 'Expired'" class="badge badge-red">
        Expired
    </div>

    <div v-else-if="discount.status === 'Draft'" class="badge badge-zinc">
        Draft
    </div>
</template>
