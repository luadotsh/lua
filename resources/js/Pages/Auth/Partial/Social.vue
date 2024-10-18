<script setup>
import Button from "@/Components/Button.vue";
</script>

<template>
    <div>
        <div class="mb-6">
            <Button
                :href="route('auth.google')"
                class="btn btn-secondary w-full space-x-2"
            >
                <img src="/images/oauth/google.svg" class="h-4 w-4" />
                <div>Continue with Google</div>
            </Button>
        </div>

        <div class="relative">
            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                <div
                    class="w-full border-t border-zinc-200 dark:border-zinc-700"
                />
            </div>
            <div
                class="relative flex justify-center text-sm font-normal leading-6"
            >
                <span class="bg-white dark:bg-zinc-900 px-6 text-zinc-400"
                    >Or continue with</span
                >
            </div>
        </div>
    </div>
</template>
