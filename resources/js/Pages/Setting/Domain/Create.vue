<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

import DialogModal from "@/Components/DialogModal.vue";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";

const form = useForm({
    domain: "",
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
    form.post(route("setting.domains.store"), {
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
        <template #title>New Domain</template>

        <template #content>
            <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                <div class="sm:col-span-6">
                    <Label for="domain" value="Domain" :required="true" />
                    <Input
                        id="domain"
                        type="text"
                        v-model="form.domain"
                        placeholder=""
                    />
                    <InputError :message="form.errors.domain" class="mt-2" />
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
                Add Domain
            </Button>
        </template>
    </DialogModal>
</template>
