<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    data: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div>
        <div
            v-if="data.links.length > 3"
            class="py-2 flex items-center justify-between"
        >
            <div class="hidden sm:block">
                <p class="text-sm text-zinc-800 dark:text-zinc-300">
                    {{ $t("pagination.showing") }}
                    <span class="font-medium">{{ data.from }}</span>
                    {{ $t("pagination.to") }}
                    <span class="font-medium">{{ data.to }}</span>
                    {{ $t("pagination.of") }}
                    <span class="font-medium">{{ data.total }}</span>
                    {{ $t("pagination.results") }}
                </p>
            </div>
            <div class="flex-1 flex justify-between sm:justify-end">
                <Link
                    v-if="data.prev_page_url"
                    :href="data.prev_page_url"
                    class="btn btn-secondary"
                    preserve-scroll
                    preserve-state
                >
                    {{ $t("pagination.previous") }}
                </Link>

                <div v-else class="btn btn-secondary cursor-not-allowed">
                    {{ $t("pagination.previous") }}
                </div>

                <Link
                    v-if="data.next_page_url"
                    :href="data.next_page_url"
                    class="ml-2 btn btn-secondary"
                    preserve-scroll
                    preserve-state
                >
                    {{ $t("pagination.next") }}
                </Link>
                <div v-else class="ml-2 cursor-not-allowed btn btn-secondary">
                    {{ $t("pagination.next") }}
                </div>
            </div>
        </div>
    </div>
</template>
