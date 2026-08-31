<script setup lang="ts">
import { CalendarDate, getLocalTimeZone } from "@internationalized/date"
import type { DateValue } from "reka-ui"
import { IconCalendar } from "@tabler/icons-vue"
import { computed, ref, shallowRef, watch } from "vue"
import { Button } from "@/components/ui/button"
import { Calendar } from "@/components/ui/calendar"
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover"
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select"
import { cn } from "@/lib/utils"
import date from "@/date"
import dayjs from "@/dayjs"

const props = defineProps<{
    modelValue: string
}>()

const emit = defineEmits<{
    "update:modelValue": [value: string]
}>()

const isOpen = ref(false)

/**
 * The picker stages its own date and time and only reports back when the user
 * confirms, so opening it to look at a date cannot change one. Shaped after
 * `PickTimePopover` in `~/Herd/trypost`.
 */
const selectedDate = shallowRef<DateValue | undefined>()

// Twelve-hour, because that is how the date reads back on the trigger and in
// every summary. The 24-hour value only exists at the edges, when parsing what
// was stored and when handing a value back.
const selectedHour = ref("12")
const selectedMinute = ref("00")
const selectedMeridiem = ref<"AM" | "PM">("AM")

const timezoneAbbr = date.getTimezoneAbbr()

const hours = Array.from({ length: 12 }, (_, i) => (i + 1).toString().padStart(2, "0"))
const meridiems = ["AM", "PM"] as const

/** 12-hour clock plus meridiem back to the 0–23 the rest of the app speaks. */
const toHour24 = (hour12: string, meridiem: string): number => {
    const h = Number(hour12) % 12

    return meridiem === "PM" ? h + 12 : h
}

/**
 * Five-minute steps, plus whatever the stored value happens to be. A link can
 * already expire at 12:37 — set through the API, or before this control looked
 * like this — and a list without it would render blank and quietly round the
 * time down on the next save.
 */
const minutes = computed(() => {
    const steps = Array.from({ length: 12 }, (_, i) => (i * 5).toString().padStart(2, "0"))

    return steps.includes(selectedMinute.value)
        ? steps
        : [...steps, selectedMinute.value].sort()
})

const toCalendarDate = (value: string) => {
    const d = dayjs(value)

    return new CalendarDate(d.year(), d.month() + 1, d.date())
}

const seed = () => {
    if (props.modelValue) {
        const d = dayjs(props.modelValue)

        selectedDate.value = toCalendarDate(props.modelValue)
        selectedHour.value = d.format("hh")
        selectedMinute.value = d.format("mm")
        selectedMeridiem.value = d.format("A") as "AM" | "PM"

        return
    }

    selectedDate.value = undefined
    selectedHour.value = "12"
    selectedMinute.value = "00"
    selectedMeridiem.value = "AM"
}

seed()

// Reopening always starts from what is saved, discarding an abandoned edit.
watch(isOpen, (open) => {
    if (open) {
        seed()
    }
})

watch(() => props.modelValue, seed)

const displayLabel = computed(() =>
    props.modelValue ? dayjs(props.modelValue).format("MMM D, YYYY h:mm A") : null,
)

const staged = computed(() => {
    if (!selectedDate.value) {
        return null
    }

    const d = selectedDate.value.toDate(getLocalTimeZone())

    return dayjs(d)
        .hour(toHour24(selectedHour.value, selectedMeridiem.value))
        .minute(Number(selectedMinute.value))
})

// Expiring a link in the past is legal — it just means "already expired" — but
// it is far more often a slip, so say so rather than block it.
const isPast = computed(() => Boolean(staged.value?.isBefore(dayjs())))

const confirm = () => {
    if (!staged.value) {
        return
    }

    emit("update:modelValue", staged.value.format("YYYY-MM-DDTHH:mm"))
    isOpen.value = false
}

const cancel = () => {
    isOpen.value = false
}

const remove = () => {
    emit("update:modelValue", "")
    isOpen.value = false
}
</script>

<template>
    <Popover v-model:open="isOpen">
        <PopoverTrigger as-child>
            <Button
                data-testid="date-time-picker"
                variant="outline"
                :class="cn('w-full justify-start text-left font-normal', !modelValue && 'text-muted-foreground')"
            >
                <IconCalendar class="mr-2 size-4 shrink-0 opacity-50" />
                <span>{{ displayLabel ?? 'Pick a date and time' }}</span>
            </Button>
        </PopoverTrigger>

        <PopoverContent class="w-auto p-0" align="start">
            <!-- month-and-year, not just arrows: an expiry a year out would
                 otherwise be twelve clicks. -->
            <Calendar v-model="selectedDate" layout="month-and-year" initial-focus />

            <div class="border-t border-border p-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="shrink-0 text-sm text-muted-foreground">Time</span>
                    <Select v-model="selectedHour">
                        <SelectTrigger class="w-[76px]">
                            <SelectValue placeholder="HH" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="h in hours" :key="h" :value="h">{{ h }}</SelectItem>
                        </SelectContent>
                    </Select>
                    <span class="text-muted-foreground">:</span>
                    <Select v-model="selectedMinute">
                        <SelectTrigger class="w-[76px]">
                            <SelectValue placeholder="MM" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="m in minutes" :key="m" :value="m">{{ m }}</SelectItem>
                        </SelectContent>
                    </Select>
                    <Select v-model="selectedMeridiem">
                        <SelectTrigger class="w-[76px]">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="m in meridiems" :key="m" :value="m">{{ m }}</SelectItem>
                        </SelectContent>
                    </Select>
                    <span v-if="timezoneAbbr" class="ml-1 shrink-0 text-xs text-muted-foreground">
                        {{ timezoneAbbr }}
                    </span>
                </div>

                <p v-if="isPast" class="mt-2 text-xs font-medium text-destructive">
                    This is in the past — the link will already be expired.
                </p>
            </div>

            <div class="flex items-center justify-between gap-2 border-t border-border p-3">
                <Button
                    v-if="modelValue"
                    type="button"
                    variant="outline"
                    size="sm"
                    class="text-destructive hover:text-destructive"
                    @click="remove"
                >
                    Remove
                </Button>
                <Button v-else type="button" variant="ghost" size="sm" @click="cancel">
                    Cancel
                </Button>

                <Button type="button" size="sm" :disabled="!selectedDate" @click="confirm">
                    Pick time
                </Button>
            </div>
        </PopoverContent>
    </Popover>
</template>
