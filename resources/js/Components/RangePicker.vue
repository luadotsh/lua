<script setup>
import { ref, watch } from "vue";
import dayjs from "@/dayjs";
import date from "@/date";

import { PhCaretDown } from "@phosphor-icons/vue";

const emit = defineEmits(["update:range"]);

const props = defineProps({
    range: {
        type: Object,
        default: () => ({
            start: null,
            end: null,
        }),
    },
    placement: {
        type: String,
        default: "left",
    },
});

const range = ref(props.range);

const setToday = () => {
    range.value = {
        group: "hour",
        start: dayjs().startOf("day").utc().format("YYYY-MM-DD"),
        end: dayjs().endOf("day").utc().format("YYYY-MM-DD"),
        timezone: date.getUserTimezone(),
    };
};

const set7days = () => {
    range.value = {
        group: "day",
        start: dayjs()
            .subtract(7, "days")
            .startOf("day")
            .utc()
            .format("YYYY-MM-DD"),
        end: dayjs().endOf("day").utc().format("YYYY-MM-DD"),
        timezone: date.getUserTimezone(),
    };
};

const set14days = () => {
    range.value = {
        group: "day",
        start: dayjs()
            .subtract(14, "days")
            .startOf("day")
            .utc()
            .format("YYYY-MM-DD"),
        end: dayjs().endOf("day").utc().format("YYYY-MM-DD"),
        timezone: date.getUserTimezone(),
    };
};

const set30days = () => {
    range.value = {
        group: "day",
        start: dayjs()
            .subtract(30, "days")
            .startOf("day")
            .utc()
            .format("YYYY-MM-DD"),
        end: dayjs().endOf("day").utc().format("YYYY-MM-DD"),
        timezone: date.getUserTimezone(),
    };
};

const set3months = () => {
    range.value = {
        group: "month",
        start: dayjs()
            .subtract(3, "months")
            .startOf("day")
            .utc()
            .format("YYYY-MM-DD"),
        end: dayjs().endOf("day").utc().format("YYYY-MM-DD"),
        timezone: date.getUserTimezone(),
    };
};

const set12months = () => {
    range.value = {
        group: "month",
        start: dayjs()
            .subtract(12, "months")
            .startOf("day")
            .utc()
            .format("YYYY-MM-DD"),
        end: dayjs().endOf("day").utc().format("YYYY-MM-DD"),
        timezone: date.getUserTimezone(),
    };
};

const setMonthToDate = () => {
    range.value = {
        group: "day",
        start: dayjs()
            .startOf("month")
            .startOf("day")
            .utc()
            .format("YYYY-MM-DD"),
        end: dayjs().endOf("day").utc().format("YYYY-MM-DD"),
        timezone: date.getUserTimezone(),
    };
};

const setQuarterToDate = () => {
    range.value = {
        group: "day",
        start: dayjs()
            .startOf("quarter")
            .startOf("day")
            .utc()
            .format("YYYY-MM-DD"),
        end: dayjs().endOf("day").utc().format("YYYY-MM-DD"),
        timezone: date.getUserTimezone(),
    };
};

const setYearToDate = () => {
    range.value = {
        group: "month",
        start: dayjs()
            .startOf("year")
            .startOf("day")
            .utc()
            .format("YYYY-MM-DD"),
        end: dayjs().endOf("day").utc().format("YYYY-MM-DD"),
        timezone: date.getUserTimezone(),
    };
};

const setAllTime = () => {
    range.value = {
        group: "month",
        start: dayjs("2024-01-10").startOf("day").utc().format("YYYY-MM-DD"),
        end: dayjs().endOf("day").utc().format("YYYY-MM-DD"),
        timezone: date.getUserTimezone(),
    };
};

watch(
    () => range.value,
    (value) => {
        if (value.group) {
            const updatedRange = {
                start: dayjs(value.start).utc().format("YYYY-MM-DD"),
                end: dayjs(value.end).utc().format("YYYY-MM-DD"),
                group: value.group,
                timezone: date.getUserTimezone(),
            };
            emit("update:range", updatedRange);
        }
    },
    { deep: true }
);
</script>

<template>
    <div class="relative">
        <VDatePicker
            v-model.range="range"
            :popover="{ visibility: 'click', placement: props.placement }"
            :isDark="true"
            color="zinc"
            :columns="2"
            timezone="utc"
            @dayclick="
                (_, event) => {
                    event.target.blur();
                }
            "
        >
            <template #default="{ togglePopover, inputValue }">
                <div
                    class="flex items-center justify-between space-x-2 bg-zinc-100 dark:bg-zinc-900 px-3 py-2 rounded-lg text-sm text-zinc-900 dark:text-zinc-300 dark:hover:text-zinc-100 hover:bg-zinc-50 dark:hover:bg-zinc-900 cursor-pointer border border-zinc-200 dark:border-zinc-700"
                    @click="() => togglePopover()"
                >
                    <div class="flex items-center">
                        {{ inputValue.start }} -
                        {{ inputValue.end }}
                    </div>
                    <PhCaretDown
                        class="h-5 w-5 text-zinc-400"
                        aria-hidden="true"
                    />
                </div>
            </template>

            <template #footer>
                <div
                    class="w-full p-3 border-t border-zinc-700 flex items-center flex-nowrap space-x-1.5"
                >
                    <button
                        class="bg-zinc-800 hover:bg-black text-zinc-300 text-xs w-full px-1.5 py-1 rounded-md whitespace-normal text-nowrap"
                        @click="setToday"
                    >
                        Today
                    </button>
                    <button
                        class="bg-zinc-800 hover:bg-black text-zinc-300 text-xs w-full px-1.5 py-1 rounded-md whitespace-normal text-nowrap"
                        @click="set7days"
                    >
                        7 days
                    </button>
                    <button
                        class="bg-zinc-800 hover:bg-black text-zinc-300 text-xs w-full px-1.5 py-1 rounded-md whitespace-normal text-nowrap"
                        @click="set14days"
                    >
                        14 days
                    </button>
                    <button
                        class="bg-zinc-800 hover:bg-black text-zinc-300 text-xs w-full px-1.5 py-1 rounded-md whitespace-normal text-nowrap"
                        @click="set30days"
                    >
                        30 days
                    </button>
                    <button
                        class="bg-zinc-800 hover:bg-black text-zinc-300 text-xs w-full px-1.5 py-1 rounded-md whitespace-normal text-nowrap"
                        @click="set3months"
                    >
                        3 Mo
                    </button>

                    <button
                        class="bg-zinc-800 hover:bg-black text-zinc-300 text-xs w-full px-1.5 py-1 rounded-md whitespace-normal text-nowrap"
                        @click="set12months"
                    >
                        12 Mo
                    </button>

                    <button
                        class="bg-zinc-800 hover:bg-black text-zinc-300 text-xs w-full px-1.5 py-1 rounded-md whitespace-normal text-nowrap"
                        @click="setMonthToDate"
                    >
                        MTD
                    </button>

                    <button
                        class="bg-zinc-800 hover:bg-black text-zinc-300 text-xs w-full px-1.5 py-1 rounded-md whitespace-normal text-nowrap"
                        @click="setQuarterToDate"
                    >
                        QTD
                    </button>

                    <button
                        class="bg-zinc-800 hover:bg-black text-zinc-300 text-xs w-full px-1.5 py-1 rounded-md whitespace-normal text-nowrap"
                        @click="setYearToDate"
                    >
                        YTD
                    </button>

                    <button
                        class="bg-zinc-800 hover:bg-black text-zinc-300 text-xs w-full px-1.5 py-1 rounded-md whitespace-normal text-nowrap"
                        @click="setAllTime"
                    >
                        All
                    </button>
                </div>
            </template>
        </VDatePicker>
    </div>
</template>
