<script setup lang="ts">
import { CalendarDate, getLocalTimeZone } from "@internationalized/date"
import type { DateValue } from "reka-ui"
import { IconCalendar, IconX } from "@tabler/icons-vue"
import { computed, ref, shallowRef, watch } from "vue"
import { Button } from "@/components/ui/button"
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover"
import { Calendar } from "@/components/ui/calendar"
import { Input } from "@/components/ui/input"
import { cn } from "@/lib/utils"
import date from "@/date"
import dayjs from "@/dayjs"

const props = defineProps<{
    modelValue: string
}>()

const emit = defineEmits<{
    'update:modelValue': [value: string]
}>()

const isOpen = ref(false)

const selectedDate = shallowRef<DateValue | undefined>(
    props.modelValue ? toCalendarDate(props.modelValue) : undefined
)
const timeValue = ref<string>(
    props.modelValue ? dayjs(props.modelValue).format("HH:mm") : "00:00"
)

function toCalendarDate(value: string) {
    const d = dayjs(value)
    return new CalendarDate(d.year(), d.month() + 1, d.date())
}

const displayLabel = computed(() => {
    if (!props.modelValue) return null
    return dayjs(props.modelValue).format("MMM D, YYYY HH:mm")
})

watch(selectedDate, (newDate) => {
    if (!newDate) return
    const d = newDate.toDate(getLocalTimeZone())
    const [h, m] = timeValue.value.split(":").map(Number)
    const result = dayjs(d).hour(h).minute(m)
    emit("update:modelValue", result.format("YYYY-MM-DDTHH:mm"))
})

watch(timeValue, (newTime) => {
    if (!selectedDate.value) return
    const d = selectedDate.value.toDate(getLocalTimeZone())
    const [h, m] = newTime.split(":").map(Number)
    const result = dayjs(d).hour(h).minute(m)
    emit("update:modelValue", result.format("YYYY-MM-DDTHH:mm"))
})

watch(() => props.modelValue, (val) => {
    if (!val) {
        selectedDate.value = undefined
        timeValue.value = "00:00"
    } else {
        selectedDate.value = toCalendarDate(val)
        timeValue.value = dayjs(val).format("HH:mm")
    }
})

const timezoneAbbr = date.getTimezoneAbbr()

// Expiring a link in the past is legal — it just means "already expired" — but
// it is far more often a slip, so say so rather than block it.
const isPast = computed(
    () => Boolean(props.modelValue) && dayjs(props.modelValue).isBefore(dayjs()),
)

const clear = () => {
    selectedDate.value = undefined
    timeValue.value = "00:00"
    emit("update:modelValue", "")
    isOpen.value = false
}
</script>

<template>
    <Popover v-model:open="isOpen">
        <PopoverTrigger as-child>
            <Button
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
                <div class="flex items-center gap-2">
                    <span class="shrink-0 text-sm text-muted-foreground">Time</span>
                    <Input type="time" v-model="timeValue" class="h-8 text-sm" />
                    <span
                        v-if="timezoneAbbr"
                        class="shrink-0 text-xs text-muted-foreground"
                    >
                        {{ timezoneAbbr }}
                    </span>
                    <Button
                        v-if="modelValue"
                        variant="ghost"
                        size="icon"
                        class="h-8 w-8 shrink-0"
                        @click="clear"
                    >
                        <IconX class="size-4" />
                        <span class="sr-only">Clear</span>
                    </Button>
                </div>

                <p v-if="isPast" class="mt-2 text-xs font-medium text-destructive">
                    This is in the past — the link will already be expired.
                </p>
            </div>
        </PopoverContent>
    </Popover>
</template>
