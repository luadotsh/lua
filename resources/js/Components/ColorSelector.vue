<template>
    <RadioGroup v-model="selected">
        <div
            class="flex items-center space-x-2 overflow-y-hidden overflow-x-auto p-2"
        >
            <RadioGroupOption
                as="template"
                v-for="color in colors"
                :key="color.name"
                :value="color"
                v-slot="{ active, checked }"
            >
                <div
                    :class="[
                        color.selectedColor,
                        active && checked ? 'ring ring-offset-1' : '',
                        !active && checked ? 'ring-2' : '',
                        'relative -m-0.5 flex cursor-pointer items-center justify-center rounded-full p-0.5 focus:outline-none',
                    ]"
                >
                    <RadioGroupLabel as="span" class="sr-only">{{
                        color.name
                    }}</RadioGroupLabel>
                    <span
                        aria-hidden="true"
                        :class="[color.bgColor, 'h-6 w-6 rounded-full ']"
                    />
                </div>
            </RadioGroupOption>
        </div>
    </RadioGroup>
</template>
<script setup>
import { ref, watch } from "vue";
import { RadioGroup, RadioGroupLabel, RadioGroupOption } from "@headlessui/vue";

const emit = defineEmits(["update:modelValue"]);

const props = defineProps({
    modelValue: {
        type: [String, Number],
        required: false,
    },
});

const colors = [
    {
        name: "red",
        bgColor: "bg-red-400",
        selectedColor: "ring-red-400",
    },
    {
        name: "orange",
        bgColor: "bg-orange-400",
        selectedColor: "ring-orange-400",
    },
    {
        name: "yellow",
        bgColor: "bg-yellow-400",
        selectedColor: "ring-yellow-400",
    },
    {
        name: "green",
        bgColor: "bg-green-400",
        selectedColor: "ring-green-400",
    },
    {
        name: "cyan",
        bgColor: "bg-cyan-400",
        selectedColor: "ring-cyan-400",
    },
    {
        name: "teal",
        bgColor: "bg-teal-400",
        selectedColor: "ring-teal-400",
    },
    {
        name: "blue",
        bgColor: "bg-blue-400",
        selectedColor: "ring-blue-400",
    },
    {
        name: "indigo",
        bgColor: "bg-indigo-400",
        selectedColor: "ring-indigo-400",
    },
    {
        name: "purple",
        bgColor: "bg-purple-400",
        selectedColor: "ring-purple-400",
    },
    {
        name: "fuchsia",
        bgColor: "bg-fuchsia-400",
        selectedColor: "ring-fuchsia-400",
    },
    {
        name: "pink",
        bgColor: "bg-pink-400",
        selectedColor: "ring-pink-400",
    },

    {
        name: "zinc",
        bgColor: "bg-zinc-400",
        selectedColor: "ring-zinc-400",
    },
];

const selected = ref(colors.find((color) => color.name === props.modelValue));

watch(
    () => selected.value,
    (value) => {
        emit("update:modelValue", value.name);
    }
);
</script>
