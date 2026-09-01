<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';
import { IconCopy } from '@tabler/icons-vue';
import { computed, ref } from 'vue';

import { Button } from '@/components/ui/button';
import { DateTimePicker } from '@/components/ui/date-time-picker';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import dayjs from '@/dayjs';
import { copyToClipboard } from '@/lib/utils';
import * as apiTokensRoutes from '@/routes/setting/api-tokens';

const token = computed(() => usePage().props.flash?.token);
const displayToken = ref(false);

const form = useForm({
    name: '',
    // Empty means the key never expires.
    expires_at: '',
});

// The picker speaks local wall time; the server is given UTC, the same way the
// link expiry does it.
const expiresAtDate = ref('');

const show = ref(false);

const open = () => {
    form.reset();
    expiresAtDate.value = '';
    show.value = true;
};

defineExpose({
    open,
});

// The dialog asks you to copy it; selecting a 700-character string by hand is
// not a reasonable way to be asked.
const copyToken = () => {
    if (token.value) {
        copyToClipboard(String(token.value), 'API token copied');
    }
};

const store = () => {
    form.expires_at = expiresAtDate.value
        ? dayjs(expiresAtDate.value).utc().format('YYYY-MM-DD HH:mm:ss')
        : '';

    form.post(apiTokensRoutes.store.url(), {
        preserveScroll: true,
        onSuccess: () => {
            displayToken.value = true;
            form.reset();
            expiresAtDate.value = '';
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
                    <Label>Expires on</Label>
                    <DateTimePicker v-model="expiresAtDate" />
                    <p class="text-xs text-muted-foreground">
                        Leave empty for a key that never expires.
                    </p>
                    <p
                        v-if="form.errors.expires_at"
                        class="text-sm text-destructive"
                    >
                        {{ form.errors.expires_at }}
                    </p>
                </div>
            </div>

            <DialogFooter>
                <Button
                    data-testid="generate-api-token"
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

            <p class="text-sm text-muted-foreground">
                Please copy your new API token. For your security, it won't be
                shown again.
            </p>

            <!--
                A token is one unbroken string, so without break-all its
                min-content width is the whole thing and it pushes the dialog
                past max-w-md and off the screen.
            -->
            <div
                v-if="token"
                class="max-h-40 min-w-0 overflow-y-auto rounded-md bg-muted px-3 py-2 font-mono text-xs break-all text-foreground"
                data-testid="api-token-value"
            >
                {{ token }}
            </div>

            <DialogFooter>
                <Button data-testid="copy-api-token" @click="copyToken">
                    <IconCopy class="size-4" />
                    Copy token
                </Button>
                <Button variant="secondary" @click="displayToken = false">
                    Close
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
