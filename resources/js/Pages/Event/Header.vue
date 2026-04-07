<script setup lang="ts">
import { ref } from "vue";
import { IconSettings, IconCircleCheck, IconCircle } from "@tabler/icons-vue";

import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { Button } from "@/components/ui/button";

import RangePicker from "@/components/RangePicker.vue";

interface Column {
    key: string;
    label: string;
    show: boolean;
}

interface Range {
    start: string;
    end: string;
    timezone: string;
    group: string;
}

const emit = defineEmits<{
    (e: "update:columns", columns: Column[]): void;
    (e: "update:range", range: Range): void;
}>();

const props = defineProps<{
    columns: Column[];
    range: Range;
}>();

const range = ref(props.range);

const setColumn = (status: Column) => {
    props.columns.forEach((item) => {
        item.show = item.key === status.key ? !item.show : item.show;
    });

    emit("update:columns", props.columns);
};
</script>

<template>
    <div
        class="w-full flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0"
    >
        <div>
            <h1 class="page-title">Events</h1>
        </div>
        <div class="flex items-center justify-between space-x-2">
            <RangePicker
                v-model:range="range"
                @update:range="$emit('update:range', $event)"
                placement="bottom-end"
                class="w-full"
            />
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <Button variant="outline" size="icon">
                        <IconSettings
                            class="h-5 w-5 text-zinc-500 dark:text-zinc-300"
                            aria-hidden="true"
                        />
                    </Button>
                </DropdownMenuTrigger>

                <DropdownMenuContent align="end" class="w-52">
                    <DropdownMenuLabel>Columns</DropdownMenuLabel>
                    <DropdownMenuItem
                        v-for="column in props.columns"
                        :key="column.key"
                        @click.prevent="setColumn(column)"
                        class="cursor-pointer flex items-center space-x-2"
                    >
                        <IconCircleCheck
                            v-if="column.show"
                            class="h-5 w-5 text-zinc-800 dark:text-zinc-300"
                        />
                        <IconCircle
                            v-else
                            class="h-5 w-5 text-zinc-800 dark:text-zinc-300"
                        />
                        <span class="truncate">{{ column.label }}</span>
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    </div>
</template>
