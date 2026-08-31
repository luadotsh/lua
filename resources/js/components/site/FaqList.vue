<script setup lang="ts">
import { IconPlus } from '@tabler/icons-vue';
import { ref } from 'vue';

/**
 * Accordion for one group of questions.
 *
 * The answers are in the DOM whether or not a panel is open, so a crawler and
 * a reader using find-in-page get all of them. The collapse is a
 * `grid-template-rows` transition rather than a measured height, which is what
 * lets it animate to auto without JavaScript reading the layout.
 */
const props = withDefaults(
    defineProps<{
        items: Array<{ question: string; answer: string }>;
        /** Index open on first render. Null leaves every one collapsed. */
        defaultOpen?: number | null;
    }>(),
    { defaultOpen: null },
);

const open = ref<number | null>(props.defaultOpen);

const toggle = (index: number): void => {
    open.value = open.value === index ? null : index;
};
</script>

<template>
    <div class="divide-y divide-border border-y border-border">
        <div v-for="(item, index) in items" :key="item.question">
            <button
                type="button"
                :data-testid="`faq-toggle-${index}`"
                class="flex w-full items-start justify-between gap-6 py-5 text-left"
                :aria-expanded="open === index"
                @click="toggle(index)"
            >
                <span class="font-medium">{{ item.question }}</span>
                <IconPlus
                    class="mt-0.5 size-4 shrink-0 text-muted-foreground transition-transform duration-200 motion-reduce:transition-none"
                    :class="{ 'rotate-45': open === index }"
                />
            </button>

            <div
                class="grid transition-[grid-template-rows] duration-200 ease-out motion-reduce:transition-none"
                :class="open === index ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]'"
            >
                <div class="overflow-hidden">
                    <p class="pb-5 leading-relaxed text-muted-foreground">
                        {{ item.answer }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
