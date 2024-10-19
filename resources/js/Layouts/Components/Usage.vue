<script setup>
import { computed } from "vue";
import { usePage, Link } from "@inertiajs/vue3";
import { PhLink, PhCaretRight, PhCursorClick } from "@phosphor-icons/vue";
import Button from "@/Components/Button.vue";

const billing = computed(() => usePage().props.billing);

const links = computed(() => {
    return `${billing.value.usage.links.used} of ${billing.value.usage.links.limit}`;
});

const events = computed(() => {
    return `${billing.value.usage.events.used} of ${billing.value.usage.events.limit}`;
});
</script>
<template>
    <div>
        <Link
            class="group flex items-center gap-x-0.5 text-sm text-zinc-500 hover:text-zinc-700"
            :href="route('setting.billing.index')"
        >
            <div>Usage</div>
            <PhCaretRight class="text-zinc-400 5 group-hover:text-zinc-500" />
        </Link>
        <div class="mt-4 flex flex-col gap-4">
            <div>
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <PhLink
                            class="text-zinc-600 w-4 h-4"
                            weight="duotone"
                        />
                        <div class="text-xs font-medium text-zinc-700">
                            Links
                        </div>
                    </div>
                    <div class="text-xs font-medium text-zinc-600">
                        {{ links }}
                    </div>
                </div>
                <div class="mt-2">
                    <div class="overflow-hidden rounded-full bg-zinc-200">
                        <div
                            class="h-1 rounded-full bg-gradient-to-r from-violet-400 to-violet-600"
                            :style="`width: ${billing.usage.links.percent}%`"
                        />
                    </div>
                </div>
            </div>
            <div>
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <PhCursorClick
                            class="text-zinc-600 w-4 h-4"
                            weight="duotone"
                        />
                        <div class="text-xs font-medium text-zinc-700">
                            Events
                        </div>
                    </div>
                    <div class="text-xs font-medium text-zinc-600">
                        {{ events }}
                    </div>
                </div>
                <div class="mt-1.5">
                    <div class="overflow-hidden rounded-full bg-zinc-200">
                        <div
                            class="h-1 rounded-full bg-gradient-to-r from-violet-400 to-violet-600"
                            :style="`width: ${billing.usage.events.percent}%`"
                        />
                    </div>
                </div>
            </div>
        </div>
        <div class="my-4">
            <div class="text-xs dark:text-zinc-400 text-zinc-600 text-center">
                {{ `Usage will reset ${billing.usage.next_reset}` }}
            </div>
        </div>
        <Button
            class="w-full btn btn-primary"
            :href="route('setting.billing.index')"
        >
            Upgrade to Pro
        </Button>
    </div>
</template>
