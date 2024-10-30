<script setup>
import { onMounted, onUnmounted, ref } from "vue";
import { router } from "@inertiajs/vue3";
import { PhSpinnerGap } from "@phosphor-icons/vue";

const { domain } = defineProps({
    domain: {
        type: Object,
        required: true,
    },
});

const intervalId = ref(null);

const validateDns = async () => {
    router.visit(route("setting.domains.validate-dns", domain.id), {
        method: "get",
        preserveState: true,
    });
};

onMounted(() => {
    if (domain.status === "pending" && !intervalId.value) {
        validateDns();

        intervalId.value = setInterval(() => {
            validateDns();
        }, 120000);
    }
});

onUnmounted(() => {
    if (intervalId.value) {
        clearInterval(intervalId.value);
    }
});
</script>

<template>
    <div v-if="domain.status === 'active'" class="badge badge-green">
        Active
    </div>
    <div
        v-else-if="domain.status === 'pending'"
        class="badge badge-orange space-x-1"
    >
        <PhSpinnerGap
            class="h-4 w-4 animate-spin hover:text-zinc-500"
            weight="duotone"
        />
        <div>Pending</div>
    </div>
</template>
