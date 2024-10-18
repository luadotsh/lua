<script setup>
import { watch, ref, computed } from "vue";
import dayjs from "@/dayjs";

const emit = defineEmits(["dateUpdate"]);

const { datetime, time } = defineProps({
    datetime: {
        type: String,
        required: false,
    },

    time: {
        type: Boolean,
        required: false,
        default: false,
    },
});

const currentDateTime = ref(datetime || dayjs().format("YYYY-MM-DD HH:mm:ss"));

const mode = computed(() => (time ? "datetime" : "date"));

watch(
    () => currentDateTime.value,
    (value) => {
        emit(
            "update",
            dayjs(currentDateTime.value).format("YYYY-MM-DD HH:mm:ss")
        );
    },
    { deep: true }
);
</script>

<template>
    <div>
        <v-date-picker
            v-model="currentDateTime"
            :mode="mode"
            color="gray"
            title-position="center"
            :popover="{ visibility: 'focus' }"
            :isDark="true"
            hide-time-header
            timezone="UTC"
            locale="en"
        >
            <template v-slot="{ inputValue, inputEvents, togglePopover }">
                <input
                    class="w-full form-input"
                    :value="inputValue"
                    v-on="inputEvents"
                />
            </template>
        </v-date-picker>
    </div>
</template>
