<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { ref } from "vue";

import DialogModal from "@/Components/DialogModal.vue";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";

const domains = usePage().props.domains;

const form = useForm({
    url: "",
    domain: "",
    key: "",
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
    form.post(route("links.store"), {
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
                    <Label for="url" value="Destination URL" :required="true" />
                    <Input
                        id="url"
                        type="text"
                        v-model="form.url"
                        placeholder=""
                    />
                    <InputError :message="form.errors.url" class="mt-2" />
                </div>

                <div class="sm:col-span-6">
                    <Label for="url" value="Domain" :required="true" />
                    <div class="relative mt-2 rounded-md shadow-sm">
                        <div
                            class="absolute inset-y-0 left-0 flex items-center"
                        >
                            <select
                                id="domain"
                                name="domain"
                                v-model="form.domain"
                                class="h-full rounded-md border-0 bg-transparent py-0 pl-3 pr-7 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm"
                            >
                                <option v-for="domain in domains" :key="domain">
                                    {{ domain }}
                                </option>
                            </select>
                        </div>
                        <input
                            type="text"
                            name="key"
                            id="key"
                            v-model="form.key"
                            class="block w-full rounded-md border-0 py-1.5 pl-16 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                        />
                    </div>
                    <InputError :message="form.errors.key" class="mt-2" />
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
