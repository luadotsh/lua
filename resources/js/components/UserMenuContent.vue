<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    IconBrightnessUp,
    IconDeviceDesktop,
    IconLogout,
    IconMoon,
    IconUser,
} from '@tabler/icons-vue';
import {
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuPortal,
    DropdownMenuSeparator,
    DropdownMenuSub,
    DropdownMenuSubContent,
    DropdownMenuSubTrigger,
} from '@/components/ui/dropdown-menu';
import UserInfo from '@/components/UserInfo.vue';
import { useAppearance } from '@/composables/useAppearance';
import { logout } from '@/routes';
import { edit as accountEdit } from '@/routes/setting/account';
import type { User } from '@/types';

type Props = {
    user: User;
};

defineProps<Props>();

const { appearance, updateAppearance } = useAppearance();
</script>

<template>
    <DropdownMenuLabel class="p-0 font-normal">
        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
            <UserInfo
                :user="user"
                :show-email="true"
                fallback-class="bg-neutral-200 text-neutral-600 dark:bg-neutral-700 dark:text-neutral-200"
            />
        </div>
    </DropdownMenuLabel>
    <DropdownMenuSeparator />
    <DropdownMenuGroup>
        <DropdownMenuItem as-child>
            <Link class="block w-full cursor-pointer" :href="accountEdit()">
                <IconUser class="size-4" />
                Account
            </Link>
        </DropdownMenuItem>
    </DropdownMenuGroup>
    <DropdownMenuSeparator />
    <DropdownMenuGroup>
        <DropdownMenuSub>
            <DropdownMenuSubTrigger class="gap-2">
                <IconBrightnessUp
                    v-if="appearance === 'light'"
                    class="size-4 text-muted-foreground"
                />
                <IconMoon
                    v-else-if="appearance === 'dark'"
                    class="size-4 text-muted-foreground"
                />
                <IconDeviceDesktop
                    v-else
                    class="size-4 text-muted-foreground"
                />
                Theme: <span class="capitalize">{{ appearance }}</span>
            </DropdownMenuSubTrigger>
            <DropdownMenuPortal>
                <DropdownMenuSubContent>
                    <DropdownMenuItem @click="updateAppearance('light')">
                        <IconBrightnessUp class="size-4" />
                        Light
                    </DropdownMenuItem>
                    <DropdownMenuItem @click="updateAppearance('dark')">
                        <IconMoon class="size-4" />
                        Dark
                    </DropdownMenuItem>
                    <DropdownMenuItem @click="updateAppearance('system')">
                        <IconDeviceDesktop class="size-4" />
                        System
                    </DropdownMenuItem>
                </DropdownMenuSubContent>
            </DropdownMenuPortal>
        </DropdownMenuSub>
    </DropdownMenuGroup>
    <DropdownMenuSeparator />
    <DropdownMenuItem as-child>
        <Link
            class="block w-full cursor-pointer"
            :href="logout()"
            as="button"
        >
            <IconLogout class="size-4" />
            Log out
        </Link>
    </DropdownMenuItem>
</template>
