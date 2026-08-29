<script setup lang="ts">
import type { DateRange } from "reka-ui"
import type { Ref } from "vue"
import { CalendarDate, getLocalTimeZone } from "@internationalized/date"
import { IconCalendar } from "@tabler/icons-vue"
import { computed, nextTick, ref, watch } from "vue"
import { useWindowSize } from "@vueuse/core"
import { Button } from "@/components/ui/button"
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover"
import { RangeCalendar } from "@/components/ui/range-calendar"
import { cn } from "@/lib/utils"
import dayjs from "@/dayjs"
import date from "@/date"

interface Range {
    start: string
    end: string
    group: string
    timezone: string
}

const props = defineProps<{
    range: Range
}>()

const emit = defineEmits<{
    'update:range': [value: Range]
}>()

const toCalendarDate = (d: string | Date) => {
    const parsed = dayjs(d)
    return new CalendarDate(parsed.year(), parsed.month() + 1, parsed.date())
}

const toDateString = (calendarDate: any): string => {
    if (!calendarDate) return dayjs().format("YYYY-MM-DD")
    return dayjs(calendarDate.toDate(getLocalTimeZone())).format("YYYY-MM-DD")
}

const determineGroup = (start: string, end: string): string => {
    const diff = dayjs(end).diff(dayjs(start), "day")
    if (diff <= 1) return "hour"
    if (diff <= 60) return "day"
    return "month"
}

const value = ref({
    start: toCalendarDate(props.range.start),
    end: toCalendarDate(props.range.end),
}) as Ref<DateRange>

const isUpdating = ref(false)
const isOpen = ref(false)
const { width } = useWindowSize()
const numberOfMonths = computed(() => width.value < 640 ? 1 : 2)

const calRange = (start: dayjs.Dayjs, end: dayjs.Dayjs) => ({
    start: new CalendarDate(start.year(), start.month() + 1, start.date()),
    end: new CalendarDate(end.year(), end.month() + 1, end.date()),
})

type Preset = { label: string; getValue: () => { start: any; end: any } }

const presetGroups: Preset[][] = [
    [
        { label: "Today", getValue: () => calRange(dayjs(), dayjs()) },
        { label: "Yesterday", getValue: () => calRange(dayjs().subtract(1, "day"), dayjs().subtract(1, "day")) },
    ],
    [
        { label: "Last 7 days", getValue: () => calRange(dayjs().subtract(6, "day"), dayjs()) },
        { label: "Last 30 days", getValue: () => calRange(dayjs().subtract(29, "day"), dayjs()) },
        { label: "Last 3 months", getValue: () => calRange(dayjs().subtract(3, "month"), dayjs()) },
        { label: "Last 6 months", getValue: () => calRange(dayjs().subtract(6, "month"), dayjs()) },
        { label: "Last 12 months", getValue: () => calRange(dayjs().subtract(12, "month").add(1, "day"), dayjs()) },
    ],
    [
        { label: "This month", getValue: () => calRange(dayjs().startOf("month"), dayjs().endOf("month")) },
        { label: "Last month", getValue: () => calRange(dayjs().subtract(1, "month").startOf("month"), dayjs().subtract(1, "month").endOf("month")) },
        { label: "Year to date", getValue: () => calRange(dayjs().startOf("year"), dayjs()) },
        { label: "Last year", getValue: () => calRange(dayjs().subtract(1, "year").startOf("year"), dayjs().subtract(1, "year").endOf("year")) },
    ],
]

const applyPreset = (preset: Preset) => {
    value.value = preset.getValue()
    isOpen.value = false
}

const displayLabel = computed(() => {
    if (!value.value.start) return "Pick a date range"
    const start = dayjs(toDateString(value.value.start)).format("MMM D, YYYY")
    const end = value.value.end ? dayjs(toDateString(value.value.end)).format("MMM D, YYYY") : null
    return end ? `${start} - ${end}` : start
})

watch(
    () => props.range,
    (newVal) => {
        if (!isUpdating.value) {
            value.value = {
                start: toCalendarDate(newVal.start),
                end: toCalendarDate(newVal.end),
            }
        }
    },
    { deep: true },
)

watch(
    value,
    (newVal) => {
        if (newVal.start && newVal.end) {
            isUpdating.value = true
            const start = toDateString(newVal.start)
            const end = toDateString(newVal.end)
            emit("update:range", {
                start,
                end,
                group: determineGroup(start, end),
                timezone: date.getUserTimezone(),
            })
            nextTick(() => {
                isUpdating.value = false
            })
        }
    },
    { deep: true },
)
</script>

<template>
    <Popover v-model:open="isOpen">
        <PopoverTrigger as-child>
            <Button
                variant="outline"
                :class="cn('w-full justify-start text-left font-normal sm:w-auto', !value && 'text-muted-foreground')"
            >
                {{ displayLabel }}
                <IconCalendar class="ml-auto size-4 opacity-50" />
            </Button>
        </PopoverTrigger>
        <PopoverContent class="w-auto p-0" align="end">
            <div class="flex flex-col sm:flex-row">
                <div class="hidden flex-col border-b border-border py-2 sm:flex sm:w-[150px] sm:shrink-0 sm:border-b-0 sm:border-r">
                    <template v-for="(group, groupIndex) in presetGroups" :key="groupIndex">
                        <div v-if="groupIndex > 0" class="border-t border-border my-1" />
                        <div class="space-y-0.5 px-2">
                            <Button
                                v-for="preset in group"
                                :key="preset.label"
                                variant="ghost"
                                size="sm"
                                class="w-full justify-start text-xs font-normal h-7"
                                @click="applyPreset(preset)"
                            >
                                {{ preset.label }}
                            </Button>
                        </div>
                    </template>
                </div>
                <div class="shrink-0">
                    <RangeCalendar
                        v-model="value"
                        initial-focus
                        :number-of-months="numberOfMonths"
                    />
                </div>
            </div>
        </PopoverContent>
    </Popover>
</template>
