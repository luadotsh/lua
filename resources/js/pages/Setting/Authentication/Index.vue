<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { IconDeviceLaptop } from '@tabler/icons-vue';
import { nextTick, ref } from 'vue';

import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { social } from '@/routes/auth';
import { password as passwordRoute } from '@/routes/setting/authentication';
import { destroy as destroyProvider } from '@/routes/setting/authentication/providers';
import { destroy as destroySessions } from '@/routes/setting/authentication/sessions';

type ConnectedAccount = {
    provider: string;
    label: string;
    connected: boolean;
};

type Session = {
    id: string;
    ip_address: string | null;
    user_agent: string | null;
    last_active: string;
    is_current: boolean;
};

defineProps<{
    hasPassword: boolean;
    connectedAccounts: ConnectedAccount[];
    sessions: Session[];
}>();

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const sessionsForm = useForm({
    password: '',
    email_confirmation: '',
});

const confirmingLogout = ref(false);
const confirmForm = ref<HTMLFormElement | null>(null);

// The button is the last thing on the page, so swapping it for the confirm
// field leaves that field below the fold: from where the eye is, the button
// simply disappears and nothing happens. Bring it into view and focus it.
const startLogout = async () => {
    confirmingLogout.value = true;

    await nextTick();

    confirmForm.value?.scrollIntoView({ block: 'center', behavior: 'smooth' });
    confirmForm.value?.querySelector('input')?.focus();
};

const updatePassword = () => {
    passwordForm.put(passwordRoute.url(), {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
    });
};

const logoutOtherSessions = () => {
    sessionsForm.delete(destroySessions.url(), {
        preserveScroll: true,
        onSuccess: () => {
            sessionsForm.reset();
            confirmingLogout.value = false;
        },
    });
};

const disconnect = (provider: string) => {
    router.delete(destroyProvider.url(provider), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Authentication" />

    <AppLayout>
        <SettingsLayout
            title="Authentication"
            description="Your password, connected accounts and browser sessions."
        >
            <!-- Password -->
            <section>
                <h2
                    class="text-base font-semibold text-zinc-800 dark:text-zinc-200"
                >
                    Password
                </h2>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    {{
                        hasPassword
                            ? 'Use a long, random password to keep your account secure.'
                            : 'You signed up with a social account. Set a password to also sign in with your email.'
                    }}
                </p>

                <form
                    class="mt-4 grid max-w-md gap-4"
                    @submit.prevent="updatePassword"
                >
                    <div v-if="hasPassword" class="grid gap-2">
                        <Label for="current_password">Current password</Label>
                        <PasswordInput
                            id="current_password"
                            v-model="passwordForm.current_password"
                            autocomplete="current-password"
                        />
                        <p
                            v-if="passwordForm.errors.current_password"
                            class="text-sm text-destructive"
                        >
                            {{ passwordForm.errors.current_password }}
                        </p>
                    </div>

                    <div class="grid gap-2">
                        <Label for="password">New password</Label>
                        <PasswordInput
                            id="password"
                            v-model="passwordForm.password"
                            autocomplete="new-password"
                        />
                        <p
                            v-if="passwordForm.errors.password"
                            class="text-sm text-destructive"
                        >
                            {{ passwordForm.errors.password }}
                        </p>
                    </div>

                    <div class="grid gap-2">
                        <Label for="password_confirmation"
                            >Confirm new password</Label
                        >
                        <PasswordInput
                            id="password_confirmation"
                            v-model="passwordForm.password_confirmation"
                            autocomplete="new-password"
                        />
                    </div>

                    <div>
                        <Button
                            type="submit"
                            :disabled="passwordForm.processing"
                        >
                            {{
                                hasPassword ? 'Update password' : 'Set password'
                            }}
                        </Button>
                    </div>
                </form>
            </section>

            <!-- Connected accounts -->
            <section>
                <h2
                    class="text-base font-semibold text-zinc-800 dark:text-zinc-200"
                >
                    Connected accounts
                </h2>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    Sign in to Lua with these providers.
                </p>

                <div v-if="connectedAccounts.length" class="mt-4 space-y-2">
                    <div
                        v-for="account in connectedAccounts"
                        :key="account.provider"
                        class="flex items-center justify-between rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900"
                    >
                        <div class="flex items-center space-x-3">
                            <img
                                :src="`/images/oauth/${account.provider}.svg`"
                                class="h-5 w-5"
                                :class="
                                    account.provider === 'github' &&
                                    'dark:invert'
                                "
                                :alt="account.label"
                            />
                            <div
                                class="text-sm font-medium text-zinc-600 dark:text-white"
                            >
                                {{ account.label }}
                            </div>
                        </div>

                        <Button
                            v-if="account.connected"
                            variant="outline"
                            size="sm"
                            :disabled="!hasPassword"
                            :title="
                                hasPassword
                                    ? undefined
                                    : 'Set a password before disconnecting your only sign-in method.'
                            "
                            @click="disconnect(account.provider)"
                        >
                            Disconnect
                        </Button>
                        <Button v-else variant="outline" size="sm" as-child>
                            <a :href="social(account.provider).url">Connect</a>
                        </Button>
                    </div>
                </div>

                <p v-else class="mt-4 text-sm text-zinc-500 dark:text-zinc-400">
                    No social providers are configured on this installation.
                </p>
            </section>

            <!-- Sessions -->
            <section v-if="sessions.length">
                <h2
                    class="text-base font-semibold text-zinc-800 dark:text-zinc-200"
                >
                    Browser sessions
                </h2>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    Where your account is currently signed in. Sign out
                    everywhere else if you do not recognise something here.
                </p>

                <div class="mt-4 space-y-2">
                    <div
                        v-for="session in sessions"
                        :key="session.id"
                        class="flex items-center justify-between rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900"
                    >
                        <div class="flex items-center space-x-3">
                            <IconDeviceLaptop class="h-5 w-5 text-zinc-400" />
                            <div>
                                <div
                                    class="text-sm font-medium text-zinc-600 dark:text-white"
                                >
                                    {{
                                        session.ip_address ?? 'Unknown address'
                                    }}
                                    <span
                                        v-if="session.is_current"
                                        class="ml-2 text-xs text-green-600"
                                        >This device</span
                                    >
                                </div>
                                <div
                                    class="max-w-md truncate text-xs text-zinc-500 dark:text-zinc-400"
                                >
                                    {{ session.user_agent ?? 'Unknown device' }}
                                    &middot; {{ session.last_active }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 max-w-md">
                    <Button
                        v-if="!confirmingLogout"
                        variant="outline"
                        @click="startLogout"
                    >
                        Sign out other sessions
                    </Button>

                    <form
                        v-else
                        ref="confirmForm"
                        class="grid gap-3"
                        @submit.prevent="logoutOtherSessions"
                    >
                        <div v-if="hasPassword" class="grid gap-2">
                            <Label for="sessions_password"
                                >Confirm your password</Label
                            >
                            <PasswordInput
                                id="sessions_password"
                                v-model="sessionsForm.password"
                                autocomplete="current-password"
                            />
                            <p
                                v-if="sessionsForm.errors.password"
                                class="text-sm text-destructive"
                            >
                                {{ sessionsForm.errors.password }}
                            </p>
                        </div>

                        <div v-else class="grid gap-2">
                            <Label for="email_confirmation"
                                >Type your email to confirm</Label
                            >
                            <Input
                                id="email_confirmation"
                                v-model="sessionsForm.email_confirmation"
                            />
                            <p
                                v-if="sessionsForm.errors.email_confirmation"
                                class="text-sm text-destructive"
                            >
                                {{ sessionsForm.errors.email_confirmation }}
                            </p>
                        </div>

                        <div class="flex gap-2">
                            <Button
                                type="submit"
                                :disabled="sessionsForm.processing"
                                >Sign out</Button
                            >
                            <Button
                                type="button"
                                variant="ghost"
                                @click="confirmingLogout = false"
                                >Cancel</Button
                            >
                        </div>
                    </form>
                </div>
            </section>
        </SettingsLayout>
    </AppLayout>
</template>
