<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { validateDns } from '@/routes/setting/domains';

interface Domain {
    id: string | number;
    status: string;
}

const { domain } = defineProps<{
    domain: Domain;
}>();

const handleValidateDns = () => {
    router.visit(validateDns(domain.id).url, {
        method: 'get',
        preserveState: true,
    });
};
</script>

<template>
    <Badge v-if="domain.status === 'active'" variant="default" class="bg-green-500 hover:bg-green-500">
        Active
    </Badge>
    <Badge
        v-else-if="domain.status === 'pending'"
        variant="destructive"
        class="cursor-pointer"
        @click="handleValidateDns"
    >
        Validate DNS
    </Badge>
    <Badge v-else variant="secondary">
        {{ domain.status }}
    </Badge>
</template>
