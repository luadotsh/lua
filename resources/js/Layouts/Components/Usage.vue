<script setup>
import { computed } from "vue";
import { usePage, Link } from "@inertiajs/vue3";
import { PhLink, PhCaretRight, PhCursorClick } from "@phosphor-icons/vue";
import Button from "@/Components/Button.vue";

const usage = computed(() => usePage().props.usage);

const links = computed(() => {
    return `${usage.value.links.used} of ${usage.value.links.limit}`;
});

const events = computed(() => {
    return `${usage.value.events.used} of ${usage.value.events.limit}`;
});
</script>
<template>
    <div>
        <Link
            class="flex items-center gap-x-0.5 text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-300"
            :href="route('setting.billing.index')"
        >
            <div>Usage</div>
            <PhCaretRight />
        </Link>
        <div class="mt-4 flex flex-col gap-4">
            <div>
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <PhLink
                            class="text-zinc-600 dark:text-zinc-400 w-4 h-4"
                        />
                        <div
                            class="text-xs font-medium text-zinc-600 dark:text-zinc-400"
                        >
                            Links
                        </div>
                    </div>
                    <div
                        class="text-xs font-medium text-zinc-600 dark:text-zinc-400"
                    >
                        {{ links }}
                    </div>
                </div>
                <div class="mt-2">
                    <div
                        class="overflow-hidden rounded-full bg-zinc-300 dark:bg-zinc-700"
                    >
                        <div
                            class="h-1 rounded-full bg-gradient-to-r from-violet-400 to-violet-600"
                            :style="`width: ${usage.links.percent}%`"
                        />
                    </div>
                </div>
            </div>
            <div>
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <PhCursorClick
                            class="text-zinc-600 dark:text-zinc-400 w-4 h-4"
                        />
                        <div
                            class="text-xs font-medium text-zinc-600 dark:text-zinc-400"
                        >
                            Events
                        </div>
                    </div>
                    <div
                        class="text-xs font-medium text-zinc-600 dark:text-zinc-400"
                    >
                        {{ events }}
                    </div>
                </div>
                <div class="mt-1.5">
                    <div
                        class="overflow-hidden rounded-full bg-zinc-300 dark:bg-zinc-700"
                    >
                        <div
                            class="h-1 rounded-full bg-gradient-to-r from-violet-400 to-violet-600"
                            :style="`width: ${usage.events.percent}%`"
                        />
                    </div>
                </div>
            </div>
        </div>
        <div class="my-4">
            <div class="text-xs dark:text-zinc-400 text-zinc-600 text-center">
                {{ `Usage will reset ${usage.next_reset}` }}
            </div>
        </div>
        <Button
            class="w-full btn btn-primary"
            :href="route('setting.billing.upgrade')"
        >
            Upgrade
        </Button>
    </div>
</template>
