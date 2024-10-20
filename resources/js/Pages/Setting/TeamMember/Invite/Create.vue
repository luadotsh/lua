<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";

import AppLayout from "@/Layouts/Master.vue";
import Button from "@/Components/Button.vue";
import FormSection from "@/Components/FormSection.vue";
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";

const show = ref(false);

const form = useForm({
    email: null,
    role: "ADMIN",
});

const roles = [
    {
        name: "admin",
        value: "ADMIN",
        description: "Full access to the entire platform.",
    },
    {
        name: "user",
        value: "USER",
        description:
            "Access to analytics, links, events, but no access to settings.",
    },
];

const store = () => {
    form.post(route("setting.invites.store"), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            show.value = false;
        },
    });
};

const open = () => {
    form.reset();
    show.value = true;
};

defineExpose({
    open,
});
</script>

<template>
    <AppLayout>
        <FormSection @submitted="store">
            <template #title> New Team Member </template>

            <template #description>
                <p>
                    Invite a new team member to your workspace. They will
                    receive an email with instructions on how to join.
                </p>
            </template>

            <template #form>
                <div class="col-span-6">
                    <Label for="email" value="Email" :required="true" />
                    <Input
                        id="email"
                        type="text"
                        v-model="form.email"
                        placeholder=""
                    />
                    <InputError :message="form.errors.email" class="mt-2" />
                </div>

                <div class="col-span-6">
                    <div class="col-span-6 lg:col-span-4">
                        <Label for="roles" value="Role" :required="true" />
                        <InputError :message="form.errors.role" class="mt-2" />

                        <div
                            class="relative z-0 mt-1 border border-zinc-200 rounded cursor-pointer"
                        >
                            <button
                                v-for="(role, i) in roles"
                                :key="role.name"
                                type="button"
                                class="relative px-4 py-3 inline-flex w-full rounded outline-none"
                                :class="{
                                    'border-t border-zinc-200  rounded-t-none':
                                        i > 0,
                                    'rounded-b-none':
                                        i != Object.keys(roles).length - 1,
                                }"
                                @click="form.role = role.value"
                            >
                                <div
                                    :class="{
                                        'opacity-50':
                                            form.role &&
                                            form.role != role.value,
                                    }"
                                >
                                    <!-- Role Name -->
                                    <div class="flex items-center">
                                        <div
                                            class="text-sm text-zinc-600"
                                            :class="{
                                                'font-semibold':
                                                    form.role == role.value,
                                            }"
                                        >
                                            {{ role.name }}
                                        </div>

                                        <svg
                                            v-if="form.role == role.value"
                                            class="ml-2 h-5 w-5 text-green-400"
                                            fill="none"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                    </div>

                                    <!-- Role Description -->
                                    <div
                                        class="mt-2 text-xs text-zinc-600 text-left"
                                    >
                                        {{ role.description }}
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
            <template #actions>
                <Button
                    @click="store"
                    :class="{
                        'btn-primary': true,
                        'opacity-25': form.processing,
                    }"
                    :disabled="form.processing"
                >
                    Send Invite
                </Button>
            </template>
        </FormSection>
    </AppLayout>
</template>
