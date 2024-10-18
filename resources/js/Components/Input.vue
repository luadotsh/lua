<script setup>
import { onMounted, ref } from "vue";

const emit = defineEmits(["update:modelValue"]);

const props = defineProps({
    modelValue: {
        type: [String, Number],
        required: false,
    },

    type: {
        type: String,
        default: "text",
    },

    class: {
        type: String,
        default: "",
    },

    id: {
        type: String,
        default: "",
    },

    name: {
        type: String,
        default: "",
    },

    placeholder: {
        type: String,
        default: "",
    },

    disabled: {
        type: Boolean,
        default: false,
    },

    readonly: {
        type: Boolean,
        default: false,
    },
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute("autofocus")) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        :type="type"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        ref="input"
        class="w-full form-input"
        :class="class"
        :placeholder="placeholder"
        :name="name"
        :id="id"
        :disabled="disabled"
        :readonly="readonly"
    />
</template>
