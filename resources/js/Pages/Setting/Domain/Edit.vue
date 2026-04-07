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
import * as domainsRoutes from "@/routes/setting/domains";

interface Domain {
    id: string | number;
    domain: string;
    not_found_url: string;
    expired_url: string;
}

const form = useForm({
    id: "" as string | number,
    domain: "",
    not_found_url: "",
    expired_url: "",
});
const show = ref(false);

const open = (domain: Domain) => {
    form.reset();

    form.id = domain.id;
    form.domain = domain.domain;
    form.not_found_url = domain.not_found_url;
    form.expired_url = domain.expired_url;

    show.value = true;
};

defineExpose({
    open,
});

const update = () => {
    form.put(domainsRoutes.update.url(form.id), {
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
        <DialogContent class="max-w-md">
            <DialogHeader>
                <DialogTitle>Edit Domain</DialogTitle>
            </DialogHeader>

            <div class="mt-4 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                <div class="sm:col-span-6 grid gap-2">
                    <Label for="domain">
                        Domain <span class="text-red-500">*</span>
                    </Label>
                    <p class="text-xs text-zinc-500">Do not include schema at the beginning of the domain.</p>
                    <Input
                        id="domain"
                        type="text"
                        v-model="form.domain"
                        placeholder="marketing.domain.com"
                    />
                    <p v-if="form.errors.domain" class="mt-2 text-sm text-red-600">{{ form.errors.domain }}</p>
                </div>

                <div class="sm:col-span-6 grid gap-2">
                    <Label for="not_found_url">Not Found URL</Label>
                    <p class="text-xs text-zinc-500">Automatically redirect users to a designated URL if a link under this domain doesn't exist.</p>
                    <Input
                        id="not_found_url"
                        type="text"
                        v-model="form.not_found_url"
                        placeholder="https://example.com"
                    />
                    <p v-if="form.errors.not_found_url" class="mt-2 text-sm text-red-600">{{ form.errors.not_found_url }}</p>
                </div>

                <div class="sm:col-span-6 grid gap-2">
                    <Label for="expired_url">Expired URL</Label>
                    <p class="text-xs text-zinc-500">Redirect users whenever any link under this domain expires.</p>
                    <Input
                        id="expired_url"
                        type="text"
                        v-model="form.expired_url"
                        placeholder="https://example.com"
                    />
                    <p v-if="form.errors.expired_url" class="mt-2 text-sm text-red-600">{{ form.errors.expired_url }}</p>
                </div>
            </div>

            <DialogFooter>
                <Button
                    @click="update"
                    :disabled="form.processing"
                    :class="{ 'opacity-25': form.processing }"
                    class="w-full"
                >
                    Update Domain
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
