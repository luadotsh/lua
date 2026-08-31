<script setup lang="ts">
import { IconChevronDown } from '@tabler/icons-vue';
import type { Component } from 'vue';

import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
import { cn } from '@/lib/utils';

/**
 * One optional behaviour of a link, closed by default.
 *
 * The row carries its own state: a configured behaviour tints its icon and puts
 * what it does on the row itself, so the screen answers "what does this link
 * do?" without opening anything. Five identical closed boxes could not.
 */
defineProps<{
    icon: Component;
    title: string;
    /** What it is configured to do. Empty means the behaviour is off. */
    summary?: string;
    /** Shown when there is no summary, to say what turning it on would do. */
    description: string;
}>();

const open = defineModel<boolean>('open', { default: false });
</script>

<template>
    <Collapsible
        v-model:open="open"
        :class="
            cn(
                'overflow-hidden rounded-lg border bg-card',
                summary ? 'border-violet-500/40' : 'border-border',
            )
        "
    >
        <CollapsibleTrigger
            class="flex w-full cursor-pointer items-center gap-3 px-3.5 py-3 text-left transition-colors hover:bg-accent/40"
        >
            <span
                :class="
                    cn(
                        'grid size-8 shrink-0 place-items-center rounded-lg',
                        summary
                            ? 'bg-violet-500/15 text-violet-400'
                            : 'bg-muted text-muted-foreground',
                    )
                "
            >
                <component :is="icon" class="size-4" />
            </span>

            <span class="flex min-w-0 flex-1 flex-col">
                <span class="text-sm font-medium text-foreground">{{
                    title
                }}</span>
                <span
                    :class="
                        cn(
                            'truncate text-xs',
                            summary
                                ? 'text-violet-400'
                                : 'text-muted-foreground',
                        )
                    "
                >
                    {{ summary || description }}
                </span>
            </span>

            <span v-if="!summary" class="shrink-0 text-xs text-muted-foreground"
                >Off</span
            >

            <IconChevronDown
                class="size-4 shrink-0 text-muted-foreground transition-transform duration-200"
                :class="{ 'rotate-180': open }"
            />
        </CollapsibleTrigger>

        <!-- The height animation comes from tw-animate-css, the same classes
             trypost uses. Without them the panel snaps open. -->
        <CollapsibleContent
            class="overflow-hidden data-[state=closed]:animate-collapsible-up data-[state=open]:animate-collapsible-down motion-reduce:data-[state=closed]:animate-none motion-reduce:data-[state=open]:animate-none"
        >
            <div
                class="flex flex-col gap-4 border-t border-border px-3.5 py-4 sm:pl-[3.4rem]"
            >
                <slot />
            </div>
        </CollapsibleContent>
    </Collapsible>
</template>
