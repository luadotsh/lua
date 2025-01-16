<script setup>
import { router } from "@inertiajs/vue3";

const { domain } = defineProps({
    domain: {
        type: Object,
        required: true,
    },
});

const validateDns = async () => {
    router.visit(route("setting.domains.validate-dns", domain.id), {
        method: "get",
        preserveState: true,
    });
};
</script>

<template>
    <div v-if="domain.status === 'active'" class="badge badge-green">
        Active
    </div>
    <div
        v-else-if="domain.status === 'pending'"
        class="badge badge-red cursor-pointer"
        @click="validateDns"
    >
        Validate DNS
    </div>
</template>
