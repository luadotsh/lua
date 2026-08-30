<script setup lang="ts">
import { IconX } from '@tabler/icons-vue';
import { computed } from 'vue';
import { countryFor } from '@/lib/countries';
import { languageLabel } from '@/lib/languages';

export type ActiveFilter = {
    dimension: string;
    values: string[];
};

const props = defineProps<{
    filters: ActiveFilter[];
}>();

defineEmits<{
    remove: [dimension: string];
    clear: [];
}>();

const LABELS: Record<string, string> = {
    referer: 'Referrer',
    utm_source: 'Source',
    utm_medium: 'Medium',
    utm_campaign: 'Campaign',
    utm_content: 'Content',
    utm_term: 'Term',
    country: 'Country',
    region: 'Region',
    city: 'City',
    browser: 'Browser',
    os: 'OS',
    device: 'Device',
    language: 'Language',
};

// The URL keeps the raw value a column holds — "BR", "pt-BR". Only the pill
// reads it back out in the words the breakdown row used.
const display = (dimension: string, value: string): string => {
    if (dimension === 'country') {
        return countryFor(value).name;
    }

    if (dimension === 'language') {
        return languageLabel(value);
    }

    return value;
};

const label = (dimension: string): string => LABELS[dimension] ?? dimension;

// One pill is removed with its own X; the shortcut only earns its place once
// there is more than one to clear.
const showClearAll = computed(() => props.filters.length > 1);
</script>

<template>
    <div
        v-if="filters.length > 0"
        class="flex flex-wrap items-center gap-2 border-b border-border bg-card px-4 py-2"
        data-testid="analytics-filters"
    >
        <span
            v-for="filter in filters"
            :key="filter.dimension"
            class="inline-flex items-stretch overflow-hidden rounded-md border border-border bg-secondary text-sm"
            :data-testid="`analytics-filter-${filter.dimension}`"
        >
            <span class="px-2 py-1 text-muted-foreground">{{ label(filter.dimension) }}</span>
            <span class="border-l border-border px-2 py-1 text-foreground">
                {{ filter.values.map((value) => display(filter.dimension, value)).join(', ') }}
            </span>
            <button
                type="button"
                class="flex cursor-pointer items-center border-l border-border px-1.5 text-muted-foreground transition-colors hover:bg-accent hover:text-foreground"
                :aria-label="`Remove the ${label(filter.dimension)} filter`"
                :data-testid="`analytics-filter-remove-${filter.dimension}`"
                @click="$emit('remove', filter.dimension)"
            >
                <IconX class="size-3.5" />
            </button>
        </span>

        <button
            v-if="showClearAll"
            type="button"
            class="cursor-pointer rounded-md px-2 py-1 text-sm text-muted-foreground transition-colors hover:bg-accent hover:text-foreground"
            data-testid="analytics-filters-clear"
            @click="$emit('clear')"
        >
            Clear filters
        </button>
    </div>
</template>
