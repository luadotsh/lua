<script setup>
import { useDarkTheme } from "@/theme";
const { isDarkTheme } = useDarkTheme();
</script>

<template>
    <div class="h-screen">
        <div class="flex min-h-full flex-1">
            <div
                class="flex flex-1 flex-col justify-center px-4 py-6 sm:px-6 sm:py-12 lg:flex-none lg:px-20 xl:px-24 bg-white dark:bg-zinc-900"
            >
                <div class="mx-auto w-full max-w-lg lg:w-[30rem]">
                    <slot />
                </div>
            </div>
            <div
                class="relative hidden w-0 flex-1 lg:flex items-center justify-center flex-col bg-zinc-100 dark:bg-zinc-800"
            >
                <img src="/images/auth/bg.png" alt="Sign up" />
            </div>
        </div>
    </div>
</template>
