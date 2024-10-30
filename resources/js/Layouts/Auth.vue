<script setup>
import { useDarkTheme } from "@/theme";
const { isDarkTheme } = useDarkTheme();
</script>

<template>
    <div
        class="flex min-h-full flex-1 flex-col justify-center px-6 py-12 lg:px-8 bg-white dark:bg-zinc-800"
    >
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <div class="flex justify-center mb-8">
                <img
                    src="/images/lua/full-color.svg"
                    class="h-16 hidden dark:block"
                />
                <img
                    src="/images/lua/full-black.svg"
                    class="h-16 block dark:hidden"
                />
            </div>
            <slot />
        </div>
    </div>
</template>
