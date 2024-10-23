<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { ref } from "vue";

import SlideOver from "@/Components/SlideOver.vue";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import Dropdown from "@/Components/Dropdown.vue";

const domains = usePage().props.domains;
const tags = usePage().props.tags;

const form = useForm({
    url: "",
    domain: domains[0],
    key: "",
    tags: [],
});
const show = ref(false);

const open = () => {
    form.reset();
    form.clearErrors();
    show.value = true;
};

const close = () => {
    form.reset();
    form.clearErrors();
    show.value = false;
};

defineExpose({
    open,
});

const store = () => {
    form.post(route("links.store"), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            form.clearErrors();
            show.value = false;
        },
    });
};
</script>

<template>
    <SlideOver max-width="2xl" :show="show" @close="close()">
        <template #title> New Link </template>

        <template #content>
            <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                <div class="col-span-6 lg:col-span-3">
                    <Label for="domain" value="Short Link" :required="true" />
                    <Dropdown
                        id="domain"
                        :options="
                            domains.map((d) => ({
                                id: d,
                                label: d,
                            }))
                        "
                        class="w-full"
                        :search="true"
                        placeholder="Select..."
                        v-model="form.domain"
                    />

                    <InputError :message="form.errors.domain" class="mt-2" />
                </div>

                <div class="col-span-6 lg:col-span-3">
                    <Label for="key" value="Custom back-half (optional)" />
                    <Input
                        id="key"
                        type="text"
                        v-model="form.key"
                        placeholder="e.g. super-link"
                    />

                    <InputError :message="form.errors.key" class="mt-2" />
                </div>
                <div class="sm:col-span-6">
                    <Label for="url" value="Destination URL" :required="true" />
                    <Input
                        id="url"
                        type="text"
                        v-model="form.url"
                        placeholder="e.g. https://example.com"
                    />
                    <InputError :message="form.errors.url" class="mt-2" />
                </div>

                <div class="sm:col-span-6">
                    <Label for="tags" value="Tags" />

                    <Dropdown
                        id="tags"
                        :options="
                            tags.map((t) => ({
                                id: t.id,
                                label: t.name,
                            }))
                        "
                        class="w-full"
                        :search="true"
                        :multiple="true"
                        placeholder="Select..."
                        v-model="form.tags"
                    />

                    <InputError :message="form.errors.tags" class="mt-2" />
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
                Generate Link
            </Button>
        </template>
    </SlideOver>
</template>
