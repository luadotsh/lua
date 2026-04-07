<script setup lang="ts">
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogDescription,
    DialogFooter,
} from "@/components/ui/dialog";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import * as invitesRoutes from "@/routes/setting/invites";

const form = useForm({
    email: null as string | null,
    role: "ADMIN",
});

const show = ref(false);

const open = () => {
    form.reset();
    show.value = true;
};

defineExpose({ open });

const store = () => {
    form.post(invitesRoutes.store.url(), {
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
        <DialogContent class="max-w-sm">
            <DialogHeader>
                <DialogTitle>Invite Team Member</DialogTitle>
                <DialogDescription>
                    They'll receive an email with instructions to join your workspace.
                </DialogDescription>
            </DialogHeader>

            <div class="grid gap-4">
                <div class="grid gap-2">
                    <Label for="email">Email <span class="text-destructive">*</span></Label>
                    <Input
                        id="email"
                        type="email"
                        v-model="form.email"
                        placeholder="colleague@example.com"
                        autofocus
                    />
                    <p v-if="form.errors.email" class="text-sm text-destructive">{{ form.errors.email }}</p>
                </div>

                <div class="grid gap-2">
                    <Label for="role">Role <span class="text-destructive">*</span></Label>
                    <Select v-model="form.role">
                        <SelectTrigger id="role" class="w-full">
                            <SelectValue placeholder="Select a role..." />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="ADMIN">
                                <div>
                                    <div class="font-medium">Admin</div>
                                    <div class="text-xs text-muted-foreground">Full access to the entire platform.</div>
                                </div>
                            </SelectItem>
                            <SelectItem value="USER">
                                <div>
                                    <div class="font-medium">User</div>
                                    <div class="text-xs text-muted-foreground">Access to analytics, links, and events.</div>
                                </div>
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <p v-if="form.errors.role" class="text-sm text-destructive">{{ form.errors.role }}</p>
                </div>
            </div>

            <DialogFooter>
                <Button
                    @click="store"
                    :disabled="form.processing"
                    :class="{ 'opacity-25': form.processing }"
                    class="w-full"
                >
                    Send Invite
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
