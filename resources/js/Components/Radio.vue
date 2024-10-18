<script setup>
import { computed } from "vue";

const emit = defineEmits(["update:checked"]);

const props = defineProps({
    checked: {
        type: [Array, Boolean, Number, String],
        default: false,
    },
    value: {
        type: [String, Number, Boolean],
        default: null,
    },
    label: {
        type: String,
        default: null,
    },
});

const proxyChecked = computed({
    get() {
        return props.checked;
    },

    set(val) {
        emit("update:checked", val);
    },
});
</script>

<template>
    <div class="flex items-center space-x-2">
        <input
            v-model="proxyChecked"
            type="radio"
            :value="value"
            class="h-4 w-4 border-zinc-300 text-black focus:ring-black"
        />
        <div class="ml-3 block text-sm font-base text-zinc-600">
            {{ label }}
        </div>
    </div>
</template>
