<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import { Button } from "@/components/ui/button";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from "@/components/ui/dialog";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import * as linksRoute from "@/routes/links";

/**
 * Creating a link asks one question: where does it go.
 *
 * The back-half is generated and the domain is the workspace default, because
 * choosing either is a decision almost nobody wants to make up front. Both are
 * changeable on the edit screen the store redirects to, along with everything
 * else the link can do.
 */
const form = useForm({
    url: "",
});

const show = ref(false);

const open = () => {
    form.reset();
    form.clearErrors();
    show.value = true;
};

defineExpose({ open });

const store = () =>
    form.post(linksRoute.store.url(), {
        onSuccess: () => {
            show.value = false;
        },
    });
</script>

<template>
    <Dialog v-model:open="show">
        <DialogContent class="sm:max-w-lg">
            <DialogHeader>
                <DialogTitle>New link</DialogTitle>
                <DialogDescription>
                    Paste the destination. You can set the back-half, tags and everything
                    else on the next screen.
                </DialogDescription>
            </DialogHeader>

            <form class="grid gap-2" @submit.prevent="store">
                <Label for="url">Destination URL</Label>
                <Input
                    id="url"
                    v-model="form.url"
                    type="text"
                    autofocus
                    placeholder="e.g. https://example.com"
                />
                <p v-if="form.errors.url" class="text-sm text-destructive">
                    {{ form.errors.url }}
                </p>
            </form>

            <DialogFooter>
                <Button :disabled="form.processing" @click="store">Continue</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
