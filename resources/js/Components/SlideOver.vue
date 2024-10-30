<script setup>
import { ref, computed } from "vue";
import {
    Dialog,
    DialogPanel,
    TransitionChild,
    TransitionRoot,
    DialogTitle,
} from "@headlessui/vue";

import { PhX } from "@phosphor-icons/vue";

const show = ref(false);

const open = () => (show.value = true);
defineExpose({ open });

const emit = defineEmits(["close"]);

const { zIndex, maxWidth, padding } = defineProps({
    zIndex: {
        default: "z-40",
    },
    maxWidth: {
        type: String,
        default: "2xl",
    },

    class: {
        default: "px-4 sm:px-6 py-6 h-full",
    },
});

const maxWidthClass = computed(() => {
    return {
        sm: "sm:max-w-sm",
        md: "sm:max-w-md",
        lg: "sm:max-w-lg",
        xl: "sm:max-w-xl",
        "2xl": "sm:max-w-2xl",
        "3xl": "sm:max-w-3xl",
        "4xl": "sm:max-w-4xl",
    }[maxWidth];
});

const close = () => {
    emit("close");
};
</script>

<template>
    <TransitionRoot as="template" :show="true" @close="close">
        <Dialog class="relative" :class="zIndex">
            <div class="fixed inset-0" />

            <div class="fixed inset-0 overflow-hidden">
                <div class="absolute inset-0 bg-opacity-40 bg-zinc-800">
                    <div
                        class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full"
                    >
                        <TransitionChild
                            as="template"
                            enter="transform transition ease-in-out duration-500 sm:duration-1000"
                            enter-from="translate-x-full"
                            enter-to="translate-x-0"
                            leave="transform transition ease-in-out duration-500 sm:duration-1000"
                            leave-from="translate-x-0"
                            leave-to="translate-x-full"
                        >
                            <DialogPanel
                                class="pointer-events-auto flex flex-col w-screen overflow-hidden border border-zinc-200 dark:border-zinc-700"
                                :class="maxWidthClass"
                            >
                                <!-- Header -->
                                <div
                                    v-if="$slots.title || $slots.description"
                                    class="bg-zinc-50 dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700 px-4 py-6 sm:px-6"
                                >
                                    <div
                                        class="flex items-start justify-between space-x-3"
                                    >
                                        <div class="space-y-1">
                                            <DialogTitle
                                                v-if="$slots.title"
                                                class="text-xl font-semibold text-zinc-800 dark:text-white leading-6"
                                            >
                                                <slot name="title" />
                                            </DialogTitle>
                                            <p
                                                v-if="$slots.description"
                                                class="text-sm text-zinc-500 dark:text-zinc-300"
                                            >
                                                <slot name="description" />
                                            </p>
                                        </div>
                                        <div class="flex h-7 items-center">
                                            <button
                                                type="button"
                                                class="rounded text-zinc-400 hover:text-zinc-500 hover:dark:text-zinc-200 outline-none"
                                                @click="close"
                                            >
                                                <PhX class="h-6 w-6 stroke-2" />
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div
                                    id="slide-over-content"
                                    class="flex h-full flex-col overflow-y-auto bg-white dark:bg-zinc-800"
                                >
                                    <div :class="class">
                                        <slot name="content" />
                                    </div>
                                </div>

                                <!-- Footer -->
                                <div
                                    v-if="$slots.footer"
                                    class="flex-shrink-0 border-t border-zinc-200 dark:border-zinc-700 px-4 py-5 sm:px-6 bg-zinc-50 dark:bg-zinc-800 z-10"
                                >
                                    <div class="flex justify-end space-x-3">
                                        <slot name="footer" />
                                    </div>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>
