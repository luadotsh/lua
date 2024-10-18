<template>
    <select
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        ref="input"
        class="w-full px-3 py-2 rounded focus:outline text-sm border-zinc-200 focus:border-black focus:ring-black 800"
    >
        <option v-if="placeholder" :value="''">
            {{ placeholder ? placeholder : "Select..." }}
        </option>
        <option v-for="(value, index) in values" :key="index" :value="index">
            {{ value }}
        </option>
    </select>
</template>

<script>
export default {
    props: {
        values: {
            type: Object,
            required: true,
        },
        modelValue: {
            required: true,
        },
        placeholder: {
            type: String,
            required: false,
            default: "",
        },
    },

    emits: ["update:modelValue"],

    methods: {
        focus() {
            this.$refs.input.focus();
        },
    },
};
</script>
