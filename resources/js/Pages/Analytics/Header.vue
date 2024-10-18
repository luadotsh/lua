<script setup>
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import { PhCaretDown } from "@phosphor-icons/vue";
import { onMounted, ref, computed, reactive } from "vue";
import { usePage, Link } from "@inertiajs/vue3";
import dayjs from "@/dayjs";
const user = computed(() => usePage().props.auth.user);

const emit = defineEmits(["update"]);

const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

const range = reactive({
    timezone: timezone,
});

const periodFilters = [
    [
        {
            name: "Today",
            key: "today",
            group: "hour",
        },
        {
            name: "Yesterday",
            key: "yesterday",
            group: "hour",
        },
    ],
    [
        {
            name: "Last 7 days",
            key: "last_7_days",
            group: "day",
        },
        {
            name: "Last 30 days",
            key: "last_30_days",
            group: "day",
        },
    ],
    [
        {
            name: "This Month",
            key: "this_month",
            group: "day",
        },
        {
            name: "Last Month",
            key: "last_month",
            group: "day",
        },
    ],
    [
        {
            name: "This Year",
            key: "this_year",
            group: "month",
        },
        {
            name: "Last 12 Months",
            key: "last_12_months",
            group: "month",
        },
    ],
];

const setCurrentFilter = (filter) => {
    switch (filter.key) {
        case "today":
            range.key = filter.key;
            range.name = filter.name;
            range.group = filter.group;
            range.start = dayjs().format("YYYY-MM-DD");
            range.end = dayjs().format("YYYY-MM-DD");

            range.start_previous = dayjs()
                .subtract(1, "day")
                .format("YYYY-MM-DD");
            range.end_previous = dayjs()
                .subtract(1, "day")
                .format("YYYY-MM-DD");

            break;

        case "yesterday":
            range.key = filter.key;
            range.name = filter.name;
            range.group = filter.group;

            range.start = dayjs().subtract(1, "day").format("YYYY-MM-DD");
            range.end = dayjs().subtract(1, "day").format("YYYY-MM-DD");

            range.start_previous = dayjs()
                .subtract(2, "day")
                .format("YYYY-MM-DD");
            range.end_previous = dayjs()
                .subtract(2, "day")
                .format("YYYY-MM-DD");

            break;

        case "last_7_days":
            range.key = filter.key;
            range.name = filter.name;
            range.group = filter.group;

            range.start = dayjs().subtract(7, "day").format("YYYY-MM-DD");
            range.end = dayjs().subtract(1, "day").format("YYYY-MM-DD");

            range.start_previous = dayjs()
                .subtract(14, "day")
                .format("YYYY-MM-DD");
            range.end_previous = dayjs()
                .subtract(7, "day")
                .format("YYYY-MM-DD");

            break;

        case "last_30_days":
            range.key = filter.key;
            range.name = filter.name;
            range.group = filter.group;

            range.start = dayjs().subtract(30, "day").format("YYYY-MM-DD");
            range.end = dayjs().subtract(1, "day").format("YYYY-MM-DD");

            range.start_previous = dayjs()
                .subtract(60, "day")
                .format("YYYY-MM-DD");
            range.end_previous = dayjs()
                .subtract(30, "day")
                .format("YYYY-MM-DD");

            break;

        case "this_month":
            range.key = filter.key;
            range.name = filter.name;
            range.group = filter.group;

            range.start = dayjs().startOf("month").format("YYYY-MM-DD");
            range.end = dayjs().format("YYYY-MM-DD");

            range.start_previous = dayjs()
                .startOf("month")
                .subtract(1, "month")
                .format("YYYY-MM-DD");
            range.end_previous = dayjs()
                .startOf("month")
                .subtract(1, "month")
                .format("YYYY-MM-DD");

            break;

        case "last_month":
            range.key = filter.key;
            range.name = filter.name;
            range.group = filter.group;

            range.start = dayjs()
                .subtract(1, "month")
                .startOf("month")
                .format("YYYY-MM-DD");
            range.end = dayjs()
                .subtract(1, "month")
                .endOf("month")
                .format("YYYY-MM-DD");

            range.start_previous = dayjs()
                .subtract(2, "month")
                .startOf("month")
                .format("YYYY-MM-DD");
            range.end_previous = dayjs()
                .subtract(2, "month")
                .endOf("month")
                .format("YYYY-MM-DD");

            break;

        case "this_year":
            range.key = filter.key;
            range.name = filter.name;
            range.group = filter.group;

            range.start = dayjs().startOf("year").format("YYYY-MM-DD");
            range.end = dayjs().format("YYYY-MM-DD");

            range.start_previous = dayjs()
                .startOf("year")
                .subtract(1, "year")
                .format("YYYY-MM-DD");
            range.end_previous = dayjs()
                .endOf("year")
                .subtract(1, "year")
                .format("YYYY-MM-DD");

            break;

        case "last_12_months":
            range.key = filter.key;
            range.name = filter.name;
            range.group = filter.group;

            range.start = dayjs().subtract(12, "month").format("YYYY-MM-DD");
            range.end = dayjs().format("YYYY-MM-DD");

            range.start_previous = dayjs()
                .subtract(24, "month")
                .format("YYYY-MM-DD");
            range.end_previous = dayjs()
                .subtract(24, "month")
                .format("YYYY-MM-DD");

            break;
    }

    emit("update", range);
};

onMounted(() => {
    setCurrentFilter(periodFilters[0][0]);
});
</script>
<template>
    <Menu as="div" class="relative inline-block text-left">
        <div>
            <MenuButton class="inline-flex w-full btn-dropdown">
                {{ range.name }}
                <PhCaretDown class="-mr-1 ml-2 h-5 w-5" aria-hidden="true" />
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
                class="absolute right-0 z-10 mt-1 w-44 origin-top-right border-2 border-gray-200 dark:border-gray-700 divide-y-2 divide-gray-100 dark:divide-zinc-700 rounded-md bg-white dark:bg-[#303030] shadow-lg focus:outline-none"
            >
                <div
                    v-for="periodFilter in periodFilters"
                    :key="periodFilter"
                    class="p-1"
                >
                    <MenuItem
                        v-for="filter in periodFilter"
                        :key="filter"
                        @click="setCurrentFilter(filter)"
                        v-slot="{ active }"
                    >
                        <div
                            class="rounded text-gray-700 dark:text-white block px-4 py-2 text-sm cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700"
                        >
                            {{ filter.name }}
                        </div>
                    </MenuItem>
                </div>
            </MenuItems>
        </transition>
    </Menu>
</template>
