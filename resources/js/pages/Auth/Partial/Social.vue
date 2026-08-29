<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { social } from '@/routes/auth';

type Provider = { provider: string; label: string };

// Only providers with credentials configured are shipped by the backend, so a
// self-hosted install that sets up one of them shows only that one.
const providers = computed(
    () => (usePage().props.socialProviders as Provider[] | undefined) ?? [],
);

const iconFor = (provider: string) => `/images/oauth/${provider}.svg`;
</script>

<template>
    <!--
        Buttons and divider are siblings on purpose: they inherit the auth
        layout's gap-6, so the divider sits the same distance from the buttons
        above it as from the form below.
    -->
    <template v-if="providers.length">
        <div class="flex flex-col gap-2">
            <Button
                v-for="provider in providers"
                :key="provider.provider"
                variant="outline"
                as-child
                class="w-full"
            >
                <a :href="social(provider.provider).url">
                    <img
                        :src="iconFor(provider.provider)"
                        class="h-4 w-4 mr-2"
                        :class="provider.provider === 'github' && 'dark:invert'"
                        :alt="provider.label"
                    />
                    Continue with {{ provider.label }}
                </a>
            </Button>
        </div>

        <div
            class="relative text-center text-sm after:absolute after:inset-0 after:top-1/2 after:z-0 after:flex after:items-center after:border-t after:border-border"
        >
            <span class="relative z-10 bg-background px-2 text-muted-foreground">
                Or continue with
            </span>
        </div>
    </template>
</template>
