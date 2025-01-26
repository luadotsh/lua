<script setup>
import { ref, watch } from "vue";
import { PhX } from "@phosphor-icons/vue";
import Input from "@/Components/Input.vue";
import date from "@/date";

const emit = defineEmits(["update:modelValue"]);

const props = defineProps({
    modelValue: {
        type: String,
        default: "",
    },

    placement: {
        type: String,
        default: "bottom-start",
    },

    mode: {
        type: String,
        default: "date",
        validator: (value) => ["date", "dateTime"].includes(value),
    },
});

const hasEmptyByUser = ref(false);
const modelValue = ref(props.modelValue);

watch(modelValue, (value) => {
    const formattedDate = value ? date.formatDateTimeForApi(value) : null;
    emit("update:modelValue", formattedDate);
});
</script>

<template>
    <VDatePicker
        v-model="modelValue"
        :popover="{
            visibility: 'click',
            placement: props.placement,
        }"
        :mode="props.mode"
        :hide-time-header="true"
        :is24hr="true"
        timezone="utc"
        :is-dark="true"
        :rules="{
            minutes: { interval: 5 },
        }"
        @dayclick="
            (_, event) => {
                event.target.blur();
            }
        "
    >
        <template #default="{ inputValue, inputEvents }">
            <div class="relative">
                <Input
                    type="text"
                    :value="inputValue"
                    v-on="inputEvents"
                    class="!py-1.5 font-medium"
                    placeholder="Select a date"
                />
                <button
                    v-if="modelValue"
                    @click="
                        hasEmptyByUser = true;
                        modelValue = null;
                    "
                >
                    <PhX
                        class="h-4 w-4 text-zinc-400 absolute right-2 top-2.5"
                        aria-hidden="true"
                    />
                </button>
            </div>
        </template>
    </VDatePicker>
</template>
