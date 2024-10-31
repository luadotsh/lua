<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { ref, computed } from "vue";

import DialogModal from "@/Components/DialogModal.vue";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";

const token = computed(() => usePage().props.flash.token);
const displayToken = ref(false);

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
    form.post(route("setting.api-tokens.store"), {
        preserveScroll: true,
        onSuccess: () => {
            displayToken.value = true;
            form.reset();
            show.value = false;
        },
    });
};
</script>

<template>
    <DialogModal max-width="md" :show="show" @close="show = null">
        <template #title>New API Token</template>

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
                Generate Token
            </Button>
        </template>
    </DialogModal>

    <!-- Token Value Modal -->
    <DialogModal
        :show="displayToken"
        @close="displayToken = false"
        max-width="md"
    >
        <template #title> API Token </template>

        <template #content>
            <div class="text-zinc-800 dark:text-zinc-300">
                Please copy your new API token. For your security, it won't be
                shown again.
            </div>

            <div
                class="mt-4 bg-zinc-100 dark:bg-zinc-800 px-4 py-2 rounded text-sm text-zinc-500 dark:text-zinc-100"
                v-if="token"
            >
                {{ token }}
            </div>
        </template>

        <template #footer>
            <Button @click="displayToken = false" class="btn btn-primary">
                Close
            </Button>
        </template>
    </DialogModal>
</template>
