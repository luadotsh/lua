<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';

defineProps<{
    data: {
        links?: unknown[];
        from?: number;
        to?: number;
        total?: number;
        prev_page_url?: string | null;
        next_page_url?: string | null;
    };
}>();
</script>

<template>
    <div v-if="(data.links?.length ?? 0) > 3" class="py-2 flex items-center justify-between">
        <div class="hidden sm:block">
            <p class="text-sm text-muted-foreground">
                Showing
                <span class="font-medium text-foreground">{{ data.from }}</span>
                to
                <span class="font-medium text-foreground">{{ data.to }}</span>
                of
                <span class="font-medium text-foreground">{{ data.total }}</span>
                results
            </p>
        </div>
        <div class="flex items-center gap-2">
            <Button
                v-if="data.prev_page_url"
                variant="outline"
                size="sm"
                as-child
            >
                <Link :href="data.prev_page_url" preserve-scroll preserve-state>
                    Previous
                </Link>
            </Button>
            <Button v-else variant="outline" size="sm" disabled>
                Previous
            </Button>

            <Button
                v-if="data.next_page_url"
                variant="outline"
                size="sm"
                as-child
            >
                <Link :href="data.next_page_url" preserve-scroll preserve-state>
                    Next
                </Link>
            </Button>
            <Button v-else variant="outline" size="sm" disabled>
                Next
            </Button>
        </div>
    </div>
</template>
