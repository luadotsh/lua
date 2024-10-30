<script setup>
import { ref } from "vue";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import { PhGear, PhCheckCircle, PhCircle } from "@phosphor-icons/vue";
import RangePicker from "@/Components/RangePicker.vue";

const emit = defineEmits(["update:columns", "update:range"]);

const props = defineProps({
    columns: Object,
    range: Object,
});

const range = ref(props.range);

const setColumn = (status) => {
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
            <Menu as="div" class="relative inline-block text-left">
                <div>
                    <MenuButton class="btn btn-secondary">
                        <PhGear
                            class="h-5 w-5 text-zinc-500 dark:text-zinc-300"
                            aria-hidden="true"
                        />
                    </MenuButton>
                </div>

                <transition
                    enter-active-class="transition ease-out duration-100"
                    enter-from-class="transform opacity-0 scale-95"
                    enter-to-class="transform opacity-100 scale-100"
                    leave-active-class="transition ease-in duration-75"
                    leave-from-class="transform opacity-100 scale-100"
                    leave-to-class="transform opacity-0 scale-95"
                >
                    <MenuItems
                        class="absolute right-0 z-10 mt-2 w-52 origin-top-right divide-y divide-zinc-100 dark:divide-zinc-700 rounded-md bg-white dark:bg-zinc-900 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                    >
                        <div class="py-1">
                            <div>
                                <div
                                    class="px-4 pt-2 text-[13px] font-medium text-black dark:text-white"
                                >
                                    Columns
                                </div>
                            </div>
                            <MenuItem
                                v-slot="{ active }"
                                v-for="column in props.columns"
                                :key="column.id"
                            >
                                <div
                                    @click.prevent="setColumn(column)"
                                    :class="[
                                        active
                                            ? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-300'
                                            : 'text-zinc-800 dark:text-zinc-300',
                                        ' px-4 py-1.5 text-sm cursor-pointer flex flex-1 items-center space-x-2',
                                    ]"
                                >
                                    <div>
                                        <PhCheckCircle
                                            v-if="column.show"
                                            class="h-5 w-5 text-zinc-800 dark:text-zinc-300"
                                            weight="fill"
                                        />
                                        <PhCircle
                                            v-else
                                            class="h-5 w-5 text-zinc-800 dark:text-zinc-300"
                                        />
                                    </div>
                                    <div class="truncate">
                                        {{ column.label }}
                                    </div>
                                </div>
                            </MenuItem>
                        </div>
                    </MenuItems>
                </transition>
            </Menu>
        </div>
    </div>
</template>
