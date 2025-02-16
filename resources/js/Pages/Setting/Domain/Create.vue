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
    not_found_url: "",
    expired_url: "",
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
    <DialogModal max-width="md" :show="show" @close="show = null">
        <template #title>New Domain</template>

        <template #content>
            <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                <div class="sm:col-span-6">
                    <Label
                        for="domain"
                        value="Domain"
                        :required="true"
                        tooltip="Do not include schema at the beginning of the domain."
                    />
                    <Input
                        id="domain"
                        type="text"
                        v-model="form.domain"
                        placeholder="marketing.domain.com"
                    />
                    <InputError :message="form.errors.domain" class="mt-2" />
                </div>

                <div class="sm:col-span-6">
                    <Label
                        for="not_found_url"
                        value="Not Found URL"
                        tooltip="Automatically redirect users to a designated URL if a link under this domain doesn’t exist."
                    />
                    <Input
                        id="not_found_url"
                        type="text"
                        v-model="form.not_found_url"
                        placeholder="https://example.com"
                    />
                    <InputError
                        :message="form.errors.not_found_url"
                        class="mt-2"
                    />
                </div>

                <div class="sm:col-span-6">
                    <Label
                        for="expired_url"
                        value="Expired URL"
                        tooltip="Redirect users whenever any link under this domain expires."
                    />
                    <Input
                        id="expired_url"
                        type="text"
                        v-model="form.expired_url"
                        placeholder="https://example.com"
                    />
                    <InputError
                        :message="form.errors.expired_url"
                        class="mt-2"
                    />
                </div>
            </div>
        </template>

        <template #footer>
            <Button
                @click="store"
                :class="{
                    'opacity-25': form.processing,
                    'btn-primary w-full': true,
                }"
                :disabled="form.processing"
            >
                Add Domain
            </Button>
        </template>
    </DialogModal>
</template>
