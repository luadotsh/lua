<script setup lang="ts">
withDefaults(defineProps<{
    title?: string;
    description?: string;
    showTerms?: boolean;
}>(), {
    showTerms: false,
});
</script>

<template>
    <div class="grid min-h-svh lg:grid-cols-2">
        <div class="flex flex-col gap-4 p-6 md:p-10">
            <div class="flex justify-start">
                <img src="/images/lua/full-color.svg" alt="Lua" class="h-8 dark:hidden" />
                <img src="/images/lua/full-white.svg" alt="Lua" class="h-8 hidden dark:block" />
            </div>

            <div class="flex flex-1 items-center justify-center">
                <div class="w-full max-w-md">
                    <div class="flex flex-col gap-6">
                        <div class="flex flex-col items-center gap-2 text-center">
                            <h1 class="text-2xl font-bold">{{ title }}</h1>
                            <p
                                v-if="description"
                                class="text-sm text-balance text-muted-foreground"
                            >
                                {{ description }}
                            </p>
                        </div>

                        <slot />
                    </div>
                </div>
            </div>

            <div
                v-if="showTerms"
                class="text-center text-xs text-muted-foreground [&_a]:underline [&_a]:underline-offset-4 [&_a]:hover:text-primary"
            >
                By continuing, you agree to our
                <a href="/terms" target="_blank">Terms of Service</a>
                and
                <a href="/privacy" target="_blank">Privacy Policy</a>.
            </div>
        </div>

        <div class="relative hidden overflow-hidden lg:block">
            <div class="absolute inset-0 bg-gradient-to-br from-zinc-900 via-zinc-950 to-zinc-950" />
            <div class="relative flex h-full flex-col items-center justify-center px-12 xl:px-16 text-center">
                <img
                    src="/images/lua/full-white.svg"
                    alt="Lua"
                    class="mb-8 h-16 opacity-90"
                />
                <h2 class="text-2xl font-bold text-white">Short links, deep analytics</h2>
                <p class="mt-3 max-w-sm text-sm leading-relaxed text-zinc-400">
                    Track every click, understand your audience, and grow your reach with Lua's powerful link platform.
                </p>
            </div>
        </div>
    </div>
</template>
