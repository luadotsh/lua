<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { ref } from "vue";

import Accordion from "@/Components/Accordion.vue";
import SlideOver from "@/Components/SlideOver.vue";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DatePicker from "@/Components/DatePicker.vue";

const domains = usePage().props.domains;
const tags = usePage().props.tags;

const form = useForm({
    url: "",
    domain: domains[0],
    key: "",
    tags: [],
    ios: "",
    android: "",
    expires_at: "",
    expired_redirect_url: "",
    password: "",
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
            <div class="flex flex-col gap-4">
                <Accordion :is-open="true">
                    <template #title> General </template>
                    <template #content>
                        <div class="col-span-6 lg:col-span-3">
                            <Label
                                for="domain"
                                value="Short Link"
                                :required="true"
                            />
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

                            <InputError
                                :message="form.errors.domain"
                                class="mt-2"
                            />
                        </div>

                        <div class="col-span-6 lg:col-span-3">
                            <Label
                                for="key"
                                value="Custom back-half (optional)"
                            />
                            <Input
                                id="key"
                                type="text"
                                v-model="form.key"
                                placeholder="e.g. super-link"
                            />

                            <InputError
                                :message="form.errors.key"
                                class="mt-2"
                            />
                        </div>
                        <div class="col-span-6">
                            <Label
                                for="url"
                                value="Destination URL"
                                :required="true"
                            />
                            <Input
                                id="url"
                                type="text"
                                v-model="form.url"
                                placeholder="e.g. https://example.com"
                            />
                            <InputError
                                :message="form.errors.url"
                                class="mt-2"
                            />
                        </div>

                        <div class="col-span-6">
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

                            <InputError
                                :message="form.errors.tags"
                                class="mt-2"
                            />
                        </div>
                    </template>
                </Accordion>

                <Accordion :is-open="false">
                    <template #title> Targeted Traffic </template>
                    <template #content>
                        <div class="col-span-6">
                            <Label
                                for="ios"
                                value="iOS URL"
                                :required="false"
                            />
                            <Input
                                id="ios"
                                type="text"
                                v-model="form.ios"
                                placeholder="e.g. https://apps.apple.com/app/333903271"
                            />
                            <InputError
                                :message="form.errors.ios"
                                class="mt-2"
                            />
                        </div>

                        <div class="col-span-6">
                            <Label
                                for="android"
                                value="Android URL"
                                :required="false"
                            />
                            <Input
                                id="android"
                                type="text"
                                v-model="form.android"
                                placeholder="e.g. https://play.google.com/store/apps/details?id=com.twitter.android"
                            />
                            <InputError
                                :message="form.errors.android"
                                class="mt-2"
                            />
                        </div>
                    </template>
                </Accordion>

                <Accordion :is-open="false">
                    <template #title> Link Password </template>
                    <template #content>
                        <div class="col-span-6">
                            <Label
                                for="password"
                                value="Password"
                                :required="false"
                            />
                            <Input
                                id="password"
                                type="text"
                                v-model="form.password"
                                placeholder="Create a password"
                            />
                            <InputError
                                :message="form.errors.password"
                                class="mt-2"
                            />
                        </div>
                    </template>
                </Accordion>

                <Accordion :is-open="false">
                    <template #title> Expiration </template>
                    <template #content>
                        <div class="col-span-6">
                            <Label
                                for="expires_at"
                                value="Date and Time"
                                :required="false"
                            />
                            <DatePicker
                                v-model="form.expires_at"
                                mode="dateTime"
                            />
                            <InputError
                                :message="form.errors.expires_at"
                                class="mt-2"
                            />
                        </div>

                        <div class="col-span-6">
                            <Label
                                for="expired_redirect_url"
                                value="Redirect users to a specific URL"
                                :required="false"
                            />
                            <Input
                                id="expired_redirect_url"
                                type="text"
                                v-model="form.expired_redirect_url"
                                placeholder="e.g. https://example.com"
                            />
                            <InputError
                                :message="form.errors.expired_redirect_url"
                                class="mt-2"
                            />
                        </div>
                    </template>
                </Accordion>
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
