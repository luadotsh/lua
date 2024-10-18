<script setup>
import { computed } from "vue";

const emit = defineEmits(["update:checked"]);

const props = defineProps({
    checked: {
        type: [Array, Boolean],
        default: false,
    },
    value: {
        type: String,
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
            type="checkbox"
            :value="value"
            class="rounded border-zinc-300 text-black shadow-sm focus:ring-black"
        />
        <div class="ms-2 text-sm text-zinc-600">
            {{ label }}
        </div>
    </div>
</template>
