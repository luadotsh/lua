<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import Button from "@/Components/Button.vue";

const isOpen = ref(false);
const deleteForm = useForm({});

const url = ref("");

defineProps({
    title: {
        type: String,
        default: "Are you sure?",
    },

    description: {
        type: String,
        default:
            "Are you sure you want to perform this action? This action cannot be undone.",
    },

    action: {
        type: String,
        default: "Delete",
    },
});

const remove = () => {
    deleteForm.delete(url.value, {
        onSuccess: () => close(),
    });
};

const open = (data) => {
    url.value = data.url;
    isOpen.value = true;
};

const close = () => {
    url.value = "";
    isOpen.value = false;
};

defineExpose({
    open,
    close,
});
</script>

<template>
    <ConfirmationModal :show="isOpen" @close="close()" maxWidth="sm">
        <template #title>
            {{ title }}
        </template>

        <template #content>
            {{ description }}
        </template>

        <template #footer>
            <Button @click="close()" class="btn-secondary !px-6">
                Cancel
            </Button>

            <Button
                class="ml-3"
                :class="{
                    'opacity-25': deleteForm.processing,
                    'btn-danger w-full': true,
                }"
                :disabled="deleteForm.processing"
                @click="remove"
            >
                {{ action }}
            </Button>
        </template>
    </ConfirmationModal>
</template>
