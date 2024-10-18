<script setup>
defineProps({
    discount: {
        type: Object,
        required: true,
    },

    showTotal: {
        type: Boolean,
        required: false,
        default: false,
    },
});
</script>

<template>
    <div class="badge badge-zinc font-mono uppercase tracking-widest">
        <div>
            {{ discount.code }}
        </div>

        <div v-if="showTotal" class="ml-1.5">
            {{ `(${discount.total_formatted})` }}
        </div>
    </div>
</template>
