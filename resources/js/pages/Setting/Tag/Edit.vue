<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";
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
import ColorSelector from "@/components/ColorSelector.vue";
import * as tagsRoutes from "@/routes/setting/tags";

interface Tag {
    id: string | number;
    name: string;
    color: string;
}

const form = useForm({
    id: "" as string | number,
    name: "",
    color: "",
});
const show = ref(false);

const open = (tag: Tag) => {
    form.reset();

    form.id = tag.id;
    form.name = tag.name;
    form.color = tag.color;

    show.value = true;
};

defineExpose({
    open,
});

const update = () => {
    form.put(tagsRoutes.update.url(form.id), {
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
                <DialogTitle>Edit Tag</DialogTitle>
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

                <div class="sm:col-span-6 grid gap-2">
                    <Label for="color">Choose color <span class="text-red-500">*</span></Label>
                    <ColorSelector id="color" v-model="form.color" />
                    <p v-if="form.errors.color" class="mt-2 text-sm text-red-600">{{ form.errors.color }}</p>
                </div>
            </div>

            <DialogFooter>
                <Button
                    @click="update"
                    :disabled="form.processing"
                    :class="{ 'opacity-25': form.processing }"
                >
                    Save Changes
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
