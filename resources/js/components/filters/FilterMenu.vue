<!-- Ported from `~/Herd/changelogfy` (Liveboard/FilterMenu.vue). -->
<script setup lang="ts">
import {
    IconCheck,
    IconChevronLeft,
    IconChevronRight,
    IconFilter,
    IconSearch,
} from '@tabler/icons-vue';
import { computed, nextTick, ref, watch } from 'vue';

import type {
    FilterCategory,
    FilterSelection,
} from '@/components/filters/filter-types';
import { Button } from '@/components/ui/button';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';

const props = defineProps<{
    categories: FilterCategory[];
    modelValue: FilterSelection;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: FilterSelection];
    change: [];
}>();

const open = ref(false);
const activeKey = ref<string | null>(null);
const search = ref('');
const searchInput = ref<HTMLInputElement | null>(null);

const totalSelected = computed((): number =>
    Object.values(props.modelValue).reduce(
        (sum, values) => sum + values.length,
        0,
    ),
);

const valueCount = (key: string): number => props.modelValue[key]?.length ?? 0;

const isSelected = (key: string, value: string): boolean =>
    (props.modelValue[key] ?? []).includes(value);

const activeCategory = computed(
    () =>
        props.categories.find((category) => category.key === activeKey.value) ??
        null,
);

const filteredCategories = computed((): FilterCategory[] => {
    const term = search.value.trim().toLowerCase();
    if (!term) {
        return props.categories;
    }

    return props.categories.filter((category) =>
        category.label.toLowerCase().includes(term),
    );
});

const filteredOptions = computed(() => {
    if (!activeCategory.value) {
        return [];
    }

    const term = search.value.trim().toLowerCase();
    if (!term) {
        return activeCategory.value.options;
    }

    return activeCategory.value.options.filter((option) =>
        option.label.toLowerCase().includes(term),
    );
});

const focusSearch = (): void => {
    nextTick(() => searchInput.value?.focus());
};

const openCategory = (key: string): void => {
    activeKey.value = key;
    search.value = '';
    focusSearch();
};

const back = (): void => {
    activeKey.value = null;
    search.value = '';
    focusSearch();
};

const toggle = (key: string, value: string): void => {
    const current = props.modelValue[key] ?? [];
    const next = current.includes(value)
        ? current.filter((item) => item !== value)
        : [...current, value];

    emit('update:modelValue', { ...props.modelValue, [key]: next });
    emit('change');
};

const clear = (): void => {
    const cleared: FilterSelection = { ...props.modelValue };
    props.categories.forEach((category) => {
        cleared[category.key] = [];
    });

    emit('update:modelValue', cleared);
    emit('change');
};

watch(open, (value) => {
    if (value) {
        activeKey.value = null;
        search.value = '';
        focusSearch();
    }
});
</script>

<template>
    <Popover v-if="categories.length > 0" v-model:open="open">
        <PopoverTrigger as-child>
            <slot name="trigger" :total="totalSelected">
                <Button
                    variant="outline"
                    size="icon"
                    class="relative bg-card"
                    data-testid="filter-menu"
                >
                    <IconFilter class="size-4" />
                    <span
                        v-if="totalSelected > 0"
                        class="absolute -top-1 -right-1 flex size-4 items-center justify-center rounded-full bg-primary text-[10px] font-medium text-primary-foreground"
                        >{{ totalSelected }}</span
                    >
                </Button>
            </slot>
        </PopoverTrigger>
        <PopoverContent align="end" class="w-60 p-0">
            <!-- Search header -->
            <div class="flex items-center gap-2 border-b border-border px-3">
                <button
                    v-if="activeCategory"
                    type="button"
                    class="-ml-1 shrink-0 text-muted-foreground transition-colors hover:text-foreground"
                    @click="back"
                >
                    <IconChevronLeft class="size-4" />
                </button>
                <IconSearch
                    v-else
                    class="size-4 shrink-0 text-muted-foreground"
                />
                <input
                    ref="searchInput"
                    v-model="search"
                    type="text"
                    autocomplete="off"
                    :placeholder="
                        activeCategory ? activeCategory.label : 'Search filter…'
                    "
                    class="h-9 w-full bg-transparent text-sm text-foreground outline-none placeholder:text-muted-foreground"
                />
            </div>

            <!-- Root: category list -->
            <div v-if="!activeCategory" class="max-h-72 overflow-auto p-1">
                <button
                    v-for="category in filteredCategories"
                    :key="category.key"
                    type="button"
                    :data-testid="`filter-category-${category.key}`"
                    class="flex w-full items-center gap-2 rounded-md px-2 py-1.5 text-sm text-foreground transition-colors hover:bg-accent"
                    @click="openCategory(category.key)"
                >
                    <component
                        :is="category.icon"
                        v-if="category.icon"
                        class="size-4 shrink-0 text-muted-foreground"
                    />
                    <span class="flex-1 truncate text-start">{{
                        category.label
                    }}</span>
                    <span
                        v-if="valueCount(category.key) > 0"
                        class="flex size-4 items-center justify-center rounded-full bg-primary/10 text-[10px] font-medium text-primary"
                        >{{ valueCount(category.key) }}</span
                    >
                    <IconChevronRight
                        class="size-4 shrink-0 text-muted-foreground"
                    />
                </button>
                <p
                    v-if="filteredCategories.length === 0"
                    class="px-2 py-6 text-center text-sm text-muted-foreground"
                >
                    Nothing matches.
                </p>
            </div>

            <!-- Category: option list -->
            <div v-else class="max-h-72 overflow-auto p-1">
                <button
                    v-for="option in filteredOptions"
                    :key="option.value"
                    type="button"
                    :data-testid="`filter-option-${option.value}`"
                    class="flex w-full items-center gap-2 rounded-md px-2 py-1.5 text-sm text-foreground transition-colors hover:bg-accent"
                    @click="toggle(activeCategory.key, option.value)"
                >
                    <slot
                        name="option-icon"
                        :category="activeCategory"
                        :option="option"
                    >
                        <span
                            v-if="option.color"
                            class="size-2.5 shrink-0 rounded-full"
                            :style="{
                                backgroundColor: option.color,
                            }"
                        />
                    </slot>
                    <span class="flex-1 truncate text-start">{{
                        option.label
                    }}</span>
                    <IconCheck
                        v-if="isSelected(activeCategory.key, option.value)"
                        class="size-4 shrink-0 text-primary"
                    />
                </button>
                <p
                    v-if="filteredOptions.length === 0"
                    class="px-2 py-6 text-center text-sm text-muted-foreground"
                >
                    Nothing matches.
                </p>
            </div>

            <!-- Footer -->
            <div v-if="totalSelected > 0" class="border-t border-border p-1">
                <button
                    type="button"
                    data-testid="filter-clear"
                    class="flex w-full items-center justify-center rounded-md px-2 py-1.5 text-sm text-muted-foreground transition-colors hover:bg-accent hover:text-foreground"
                    @click="clear"
                >
                    Clear filters
                </button>
            </div>
        </PopoverContent>
    </Popover>
</template>
