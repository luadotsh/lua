<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import {
    IconBrightnessUp,
    IconBuilding,
    IconCheck,
    IconCreditCard,
    IconDeviceDesktop,
    IconLogout,
    IconMoon,
    IconPlus,
    IconShieldLock,
    IconUser,
} from '@tabler/icons-vue';
import { computed } from 'vue';
import UserInfo from '@/components/UserInfo.vue';
import { Avatar } from '@/components/ui/avatar';
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
import { useAppearance } from '@/composables/useAppearance';
import { logout } from '@/routes';
import { edit as accountEdit } from '@/routes/setting/account';
import { edit as authenticationEdit } from '@/routes/setting/authentication';
import { index as billingIndex } from '@/routes/setting/billing';
import { edit as workspaceEdit } from '@/routes/setting/workspace';
import {
    create as workspacesCreate,
    updateCurrent as workspacesUpdateCurrent,
} from '@/routes/workspaces';

/**
 * The sidebar's one menu: who you are, what you can change about the account,
 * and which workspace you are in. Ported from `~/Herd/trypost`, where a single
 * dropdown at the top does this job — two menus at opposite ends of the sidebar
 * only make you guess which one holds what.
 */
const page = usePage();
const user = computed(() => page.props.auth.user);
const workspaces = computed(() => page.props.auth.user?.workspaces ?? []);
const currentWorkspaceId = computed(() => page.props.auth.user?.current_workspace_id);

const { appearance, updateAppearance } = useAppearance();

const switchWorkspace = (workspaceId: string) => {
    if (workspaceId === currentWorkspaceId.value) {
        return;
    }

    router.put(
        workspacesUpdateCurrent().url,
        { workspace_id: workspaceId },
        { preserveScroll: true },
    );
};
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
        <DropdownMenuItem as-child>
            <Link class="block w-full cursor-pointer" :href="authenticationEdit()">
                <IconShieldLock class="size-4" />
                Authentication
            </Link>
        </DropdownMenuItem>
        <DropdownMenuItem as-child>
            <Link class="block w-full cursor-pointer" :href="workspaceEdit()">
                <IconBuilding class="size-4" />
                Workspace
            </Link>
        </DropdownMenuItem>
        <DropdownMenuItem as-child>
            <Link class="block w-full cursor-pointer" :href="billingIndex()">
                <IconCreditCard class="size-4" />
                Billing
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
                <IconDeviceDesktop v-else class="size-4 text-muted-foreground" />
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

    <DropdownMenuLabel class="text-xs font-medium text-muted-foreground">
        Workspaces
    </DropdownMenuLabel>

    <DropdownMenuGroup>
        <DropdownMenuItem
            v-for="workspace in workspaces"
            :key="workspace.id"
            class="gap-2"
            @click="switchWorkspace(workspace.id)"
        >
            <Avatar
                :src="workspace.logo_url"
                :name="workspace.name"
                class="h-6 w-6 shrink-0 rounded-md"
                fallback-class="text-[10px]"
            />
            <span class="min-w-0 flex-1 truncate">{{ workspace.name }}</span>
            <IconCheck
                v-if="workspace.id === currentWorkspaceId"
                class="size-4 shrink-0 text-foreground"
                stroke-width="2.5"
            />
        </DropdownMenuItem>
        <DropdownMenuItem as-child>
            <Link class="block w-full cursor-pointer" :href="workspacesCreate()">
                <IconPlus class="size-4" />
                Create Workspace
            </Link>
        </DropdownMenuItem>
    </DropdownMenuGroup>

    <DropdownMenuSeparator />

    <DropdownMenuItem as-child>
        <Link class="block w-full cursor-pointer" :href="logout()" as="button">
            <IconLogout class="size-4" />
            Log out
        </Link>
    </DropdownMenuItem>
</template>
