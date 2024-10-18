<script setup>
import Modal from "./Modal.vue";

const emit = defineEmits(["close"]);

defineProps({
    show: {
        default: false,
    },
});

const close = () => {
    emit("close");
};
</script>

<template>
    <Modal :show="show" :closeable="false" @close="close">
        <div class="bg-white dark:bg-zinc-900 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start w-full">
                <div class="sm:mt-0 w-full">
                    <h3
                        class="text-lg text-center font-semibold text-zinc-800 dark:text-zinc-300"
                    >
                        <slot name="title" />
                    </h3>

                    <div
                        class="mt-4 text-sm text-center text-zinc-600 dark:text-zinc-400"
                    >
                        <slot name="content" />
                    </div>
                </div>
            </div>
        </div>

        <div
            class="flex flex-row justify-end px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-right"
        >
            <slot name="footer" />
        </div>
    </Modal>
</template>
