<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { ref } from "vue";

import DialogModal from "@/Components/DialogModal.vue";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import ColorSelector from "@/Components/ColorSelector.vue";

const form = useForm({
    name: "",
    color: "",
});
const show = ref(false);

const open = () => {
    form.reset();
    show.value = true;
};

defineExpose({
    open,
});

const store = () => {
    form.post(route("setting.tags.store"), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            show.value = false;
        },
    });
};
</script>

<template>
    <DialogModal max-width="xl" :show="show" @close="show = null">
        <template #title>New Tag</template>

        <template #content>
            <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                <div class="sm:col-span-6">
                    <Label for="name" value="Name" :required="true" />
                    <Input
                        id="name"
                        type="text"
                        v-model="form.name"
                        placeholder=""
                    />
                    <InputError :message="form.errors.name" class="mt-2" />
                </div>

                <div class="sm:col-span-6">
                    <Label for="color" value="Choose color" :required="true" />
                    <ColorSelector id="color" v-model="form.color" />
                    <InputError :message="form.errors.color" class="mt-2" />
                </div>
            </div>
        </template>

        <template #footer>
            <Button
                @click="store"
                :class="{
                    'opacity-25': form.processing,
                    'btn-primary': true,
                }"
                :disabled="form.processing"
            >
                Add Tag
            </Button>
        </template>
    </DialogModal>
</template>
