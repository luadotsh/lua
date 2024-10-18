<script setup>
defineProps({
    comment: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div v-if="comment.status === 'PENDING'" class="badge badge-orange">
        Pending
    </div>
    <div v-else-if="comment.status === 'APPROVED'" class="badge badge-green">
        Approved
    </div>
    <div v-else-if="comment.status === 'REJECTED'" class="badge badge-red">
        Rejected
    </div>
</template>
