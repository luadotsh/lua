<script setup>
import { PhLink, PhCaretRight, PhCursorClick } from "@phosphor-icons/vue";
</script>
<template>
    <div>
        <a
            class="group flex items-center gap-0.5 text-sm font-normal text-neutral-500 transition-colors hover:text-neutral-700"
            href="/changelogfy/settings/billing"
        >
            <div>Usage</div>

            <PhCaretRight
                class="size-3 text-neutral-400 transition-[color,transform] group-hover:translate-x-0.5 group-hover:text-neutral-500"
            />
        </a>
        <div class="mt-4 flex flex-col gap-4">
            <div>
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <PhLink
                            class="text-neutral-600 w-4 h-4"
                            weight="duotone"
                        />
                        <div class="text-xs font-medium text-neutral-700">
                            Links
                        </div>
                    </div>
                    <div class="text-xs font-medium text-neutral-600">
                        3 of 25
                    </div>
                </div>
                <div class="mt-1.5">
                    <div
                        class="h-0.5 w-full overflow-hidden rounded-full bg-neutral-900/10 transition-colors"
                    >
                        <div
                            class="animate-slide-right-fade size-full"
                            style="--offset: -100%"
                        >
                            <div
                                class="size-full rounded-full bg-gradient-to-r from-transparent to-blue-600"
                                style="transform: translateX(-88%)"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <PhCursorClick
                            class="text-neutral-600 w-4 h-4"
                            weight="duotone"
                        />
                        <div class="text-xs font-medium text-neutral-700">
                            Events
                        </div>
                    </div>
                    <div class="text-xs font-medium text-neutral-600">
                        3 of 1,000
                    </div>
                </div>
                <div class="mt-1.5">
                    <div
                        class="h-0.5 w-full overflow-hidden rounded-full bg-neutral-900/10 transition-colors"
                    >
                        <div
                            class="animate-slide-right-fade size-full"
                            style="--offset: -100%"
                        >
                            <div
                                class="size-full rounded-full bg-gradient-to-r from-transparent to-blue-600"
                                style="transform: translateX(-100%)"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-3">
            <p class="text-xs text-neutral-900/40">
                Usage will reset Nov 8, 2024
            </p>
        </div>
        <a
            class="transition-all border-black bg-black text-white hover:bg-gray-800 hover:ring-4 hover:ring-gray-200 mt-4 flex h-9 items-center justify-center rounded-md border px-4 text-sm"
            href="/changelogfy/upgrade"
        >
            Upgrade to Pro
        </a>
    </div>
</template>
