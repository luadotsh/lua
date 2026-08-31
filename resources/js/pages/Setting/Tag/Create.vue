<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

import HexColorInput from '@/components/HexColorInput.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import * as tagsRoutes from '@/routes/setting/tags';

// A tag has to have a colour, so the form opens on one instead of on an empty
// required field the user has to discover.
const DEFAULT_COLOR = '#a1a1aa';

const form = useForm({
    name: '',
    color: DEFAULT_COLOR,
});
const show = ref(false);

const open = () => {
    form.reset();
    form.color = DEFAULT_COLOR;
    show.value = true;
};

defineExpose({
    open,
});

const store = () => {
    form.post(tagsRoutes.store.url(), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            show.value = false;
        },
    });
};
</script>

<template>
    <Dialog :open="show" @update:open="(val) => (show = val)">
        <DialogContent class="max-w-xl">
            <DialogHeader>
                <DialogTitle>New Tag</DialogTitle>
            </DialogHeader>

            <div class="mt-4 grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-6">
                <div class="grid gap-2 sm:col-span-6">
                    <Label for="name"
                        >Name <span class="text-red-500">*</span></Label
                    >
                    <Input
                        id="name"
                        type="text"
                        v-model="form.name"
                        placeholder=""
                    />
                    <p
                        v-if="form.errors.name"
                        class="mt-2 text-sm text-red-600"
                    >
                        {{ form.errors.name }}
                    </p>
                </div>

                <div class="grid gap-2 sm:col-span-6">
                    <Label for="color">Color</Label>
                    <HexColorInput v-model="form.color" name="color" />
                    <p
                        v-if="form.errors.color"
                        class="mt-2 text-sm text-red-600"
                    >
                        {{ form.errors.color }}
                    </p>
                </div>
            </div>

            <DialogFooter>
                <Button
                    @click="store"
                    :disabled="form.processing"
                    :class="{ 'opacity-25': form.processing }"
                >
                    Add Tag
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
