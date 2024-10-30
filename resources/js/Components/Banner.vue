<script setup>
import { computed, ref, watch } from "vue";
import { usePage } from "@inertiajs/vue3";

import {
    PhInfo,
    PhXCircle,
    PhCheckCircle,
    PhWarningCircle,
} from "@phosphor-icons/vue";

const show = ref(true);
const style = computed(() => usePage().props.flash?.bannerStyle || "success");
const message = computed(() => usePage().props.flash?.banner || "");

watch(message, async () => {
    show.value = true;
    setTimeout(() => {
        show.value = false;
    }, 3000);
});
</script>

<template>
    <div
        v-if="show && message"
        class="fixed inset-0 flex px-4 py-6 pointer-events-none sm:p-6 sm:items-start z-50"
    >
        <div
            class="w-full flex flex-col absolute bottom-1 mb-4 items-center space-y-4 justify-center"
        >
            <transition
                enter-active-class="transform ease-out duration-300 transition"
                enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
                leave-active-class="transition ease-in duration-100"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    class="max-w-sm w-full bg-zinc-900 shadow-2xl rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden"
                >
                    <div class="px-4 py-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <PhCheckCircle
                                    v-if="style == 'success'"
                                    class="h-5 w-5 text-green-400 stroke-2"
                                />
                                <PhXCircle
                                    v-if="style == 'danger'"
                                    class="h-5 w-5 text-red-400 stroke-2"
                                />
                                <PhInfo
                                    v-if="style == 'info'"
                                    class="h-5 w-5 text-blue-400 stroke-2"
                                />
                                <PhWarningCircle
                                    v-if="style == 'warning'"
                                    class="h-5 w-5 text-yellow-400 stroke-2"
                                />
                            </div>
                            <div
                                class="ml-2 w-0 flex-1 text-sm text-white font-medium"
                            >
                                {{ message }}
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </div>
</template>
