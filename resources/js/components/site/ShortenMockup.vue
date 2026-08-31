<script setup lang="ts">
import { IconArrowNarrowRight, IconCheck, IconLink } from '@tabler/icons-vue';

/**
 * The moment a long URL becomes a short one, on the reader's own domain.
 *
 * Drawn rather than screenshotted for the same reason as the analytics
 * mockup: it restyles with the theme, stays sharp, and cannot go stale
 * against a UI screenshot nobody remembered to retake.
 */
</script>

<template>
    <div
        data-testid="shorten-mockup"
        class="overflow-hidden site-card shadow-sm"
    >
        <div class="space-y-5 p-6">
            <div>
                <p class="text-xs font-medium text-muted-foreground">Destination</p>
                <p
                    class="mt-2 truncate rounded-lg border border-border bg-muted/40 px-3 py-2.5 font-mono text-sm text-muted-foreground"
                >
                    https://example.com/collections/spring-2026?variant=42
                </p>
            </div>

            <div class="flex justify-center" aria-hidden="true">
                <IconArrowNarrowRight class="size-5 rotate-90 text-muted-foreground" />
            </div>

            <div>
                <p class="text-xs font-medium text-muted-foreground">Short link</p>
                <div
                    class="mt-2 flex items-center gap-2 rounded-lg border border-foreground/20 bg-background px-3 py-2.5"
                >
                    <IconLink class="size-4 shrink-0 text-muted-foreground" />
                    <p class="truncate font-mono text-sm">go.example.com/spring</p>
                    <span
                        class="ml-auto inline-flex shrink-0 items-center gap-1 text-xs text-emerald-600"
                    >
                        <IconCheck class="size-3.5" />
                        Copied
                    </span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4 border-t border-border p-6">
            <!--
                A QR drawn as a grid rather than a real encoder: this is
                decoration, and shipping a QR library to a marketing page to
                render a code nobody will scan would be silly. The pattern is
                fixed so the server and the client agree on it.
            -->
            <div
                class="grid shrink-0 grid-cols-7 gap-px rounded-md border border-border bg-background p-2"
                aria-hidden="true"
            >
                <span
                    v-for="(filled, index) in [
                        1,1,1,0,1,1,1,
                        1,0,1,0,1,0,1,
                        1,1,1,0,1,1,1,
                        0,0,0,1,0,0,0,
                        1,1,1,0,1,0,1,
                        1,0,1,1,0,1,1,
                        1,1,1,0,1,1,0,
                    ]"
                    :key="index"
                    class="size-1.5 rounded-[1px]"
                    :class="filled ? 'bg-foreground' : 'bg-transparent'"
                />
            </div>

            <div class="min-w-0">
                <p class="text-sm font-medium">Every link has a QR code</p>
                <p class="mt-1 text-sm text-muted-foreground">
                    Scans are counted apart from clicks, so print and screen
                    never end up in the same number.
                </p>
            </div>
        </div>
    </div>
</template>
