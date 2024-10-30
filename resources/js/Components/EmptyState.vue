<script setup>
import { Link } from "@inertiajs/vue3";

import { PhArrowRight } from "@phosphor-icons/vue";
defineProps({
    title: String,
    description: String,
    icon: Object,
    buttonTitle: String,
    buttonLink: String,
    method: {
        type: String,
        default: "get",
    },
});
</script>

<template>
    <div
        class="absolute inset-0 bg-zinc-100 dark:bg-zinc-900 z-20 top-[56px] sm:top-0"
    >
        <div class="lg:pl-64 pl-2 pr-2 py-2 overflow-hidden w-full h-full">
            <div
                class="bg-white dark:bg-zinc-800 w-full h-full border border-zinc-300 dark:border-zinc-700 rounded-lg shadow-lg dark:shadow-none overflow-hidden"
            >
                <div
                    class="max-w-sm min-h-full w-full flex flex-1 flex-col justify-center mx-auto px-6 py-4"
                >
                    <div
                        class="flex justify-center items-center w-[46px] h-[46px] bg-zinc-100 dark:bg-zinc-900 rounded"
                    >
                        <component
                            :is="icon"
                            class="w-6 h-6 text-zinc-600 dark:text-white stroke-2"
                        />
                    </div>

                    <h2
                        class="mt-5 font-semibold text-zinc-800 dark:text-white"
                    >
                        {{ title }}
                    </h2>
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                        {{ description }}
                    </p>

                    <div v-if="buttonLink" class="mt-5 grid sm:flex gap-2">
                        <Link
                            :href="buttonLink"
                            :method="method"
                            as="button"
                            class="btn btn-primary space-x-2"
                        >
                            <div>{{ buttonTitle }}</div>
                            <PhArrowRight
                                class="h-5 w-5 stroke-2"
                                weight="bold"
                            />
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
