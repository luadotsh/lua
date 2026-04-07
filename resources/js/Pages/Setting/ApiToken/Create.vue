<script setup lang="ts">
import { useForm, usePage } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogFooter,
} from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import * as apiTokensRoutes from "@/routes/setting/api-tokens";

const token = computed(() => usePage().props.flash?.token);
const displayToken = ref(false);

const form = useForm({
    name: "",
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
    form.post(apiTokensRoutes.store.url(), {
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
    <Dialog :open="show" @update:open="(val) => (show = val)">
        <DialogContent class="max-w-md">
            <DialogHeader>
                <DialogTitle>New API Token</DialogTitle>
            </DialogHeader>

            <div class="mt-4 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                <div class="sm:col-span-6 grid gap-2">
                    <Label for="name">Name <span class="text-red-500">*</span></Label>
                    <Input
                        id="name"
                        type="text"
                        v-model="form.name"
                        placeholder=""
                    />
                    <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">{{ form.errors.name }}</p>
                </div>
            </div>

            <DialogFooter>
                <Button
                    @click="store"
                    :disabled="form.processing"
                    :class="{ 'opacity-25': form.processing }"
                >
                    Generate Token
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>

    <!-- Token Value Modal -->
    <Dialog :open="displayToken" @update:open="(val) => (displayToken = val)">
        <DialogContent class="max-w-md">
            <DialogHeader>
                <DialogTitle>API Token</DialogTitle>
            </DialogHeader>

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

            <DialogFooter>
                <Button @click="displayToken = false">
                    Close
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
