<script setup>
import Modal from "./Modal.vue";

const emit = defineEmits(["close"]);

defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: "2xl",
    },
    closeable: {
        type: Boolean,
        default: true,
    },
});

const close = () => {
    emit("close");
};
</script>

<template>
    <Modal
        :show="show"
        :max-width="maxWidth"
        :closeable="closeable"
        @close="close"
    >
        <div class="px-6 py-4 w-full">
            <div class="text-lg font-medium text-zinc-900 dark:text-zinc-300">
                <slot name="title" />
            </div>

            <div
                v-if="$slots.description"
                class="text-sm font-normal text-zinc-500 mt-1"
            >
                <slot name="description" />
            </div>

            <div class="mt-4 text-sm text-zinc-600 w-full">
                <slot name="content" />
            </div>
        </div>

        <div
            v-if="$slots.footer"
            class="flex flex-row justify-end space-x-2 px-6 py-4 bg-zinc-100 dark:bg-zinc-900 border-t border-zinc-100 dark:border-zinc-800 text-right rounded-b-xl"
        >
            <slot name="footer" />
        </div>
    </Modal>
</template>
