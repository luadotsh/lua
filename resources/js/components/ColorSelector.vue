<script setup lang="ts">
import { ref, watch } from "vue";

const emit = defineEmits<{
    (e: "update:modelValue", value: string): void;
}>();

const props = defineProps<{
    modelValue?: string;
}>();

const colors = [
    { name: "red", bgColor: "bg-red-400", ringColor: "ring-red-400" },
    { name: "orange", bgColor: "bg-orange-400", ringColor: "ring-orange-400" },
    { name: "yellow", bgColor: "bg-yellow-400", ringColor: "ring-yellow-400" },
    { name: "green", bgColor: "bg-green-400", ringColor: "ring-green-400" },
    { name: "cyan", bgColor: "bg-cyan-400", ringColor: "ring-cyan-400" },
    { name: "teal", bgColor: "bg-teal-400", ringColor: "ring-teal-400" },
    { name: "blue", bgColor: "bg-blue-400", ringColor: "ring-blue-400" },
    { name: "indigo", bgColor: "bg-indigo-400", ringColor: "ring-indigo-400" },
    { name: "purple", bgColor: "bg-purple-400", ringColor: "ring-purple-400" },
    { name: "fuchsia", bgColor: "bg-fuchsia-400", ringColor: "ring-fuchsia-400" },
    { name: "pink", bgColor: "bg-pink-400", ringColor: "ring-pink-400" },
    { name: "zinc", bgColor: "bg-zinc-400", ringColor: "ring-zinc-400" },
];

const selected = ref(props.modelValue ?? "");

watch(selected, (value) => {
    emit("update:modelValue", value);
});
</script>

<template>
    <div class="flex items-center space-x-2 overflow-x-auto p-2">
        <button
            v-for="color in colors"
            :key="color.name"
            type="button"
            class="relative flex cursor-pointer items-center justify-center rounded-full p-0.5 focus:outline-none"
            :class="[selected === color.name ? `ring-2 ${color.ringColor}` : '']"
            @click="selected = color.name"
        >
            <span class="sr-only">{{ color.name }}</span>
            <span
                aria-hidden="true"
                :class="[color.bgColor, 'h-6 w-6 rounded-full block']"
            />
        </button>
    </div>
</template>
