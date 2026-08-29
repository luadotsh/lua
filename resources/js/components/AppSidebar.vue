<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import {
    IconChevronRight,
    IconChartBar,
    IconClick,
    IconCalendarEvent,
    IconSettings,
    IconPlus,
    IconLink,
} from '@tabler/icons-vue';
import { computed } from 'vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Avatar } from '@/components/ui/avatar';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { index as analyticsIndex } from '@/routes/analytics';
import { index as linksIndex } from '@/routes/links';
import { index as eventsIndex } from '@/routes/events';
import { edit as settingsAccount } from '@/routes/setting/account';
import { updateCurrent as workspacesUpdateCurrent, create as workspacesCreate } from '@/routes/workspaces';
import { index as billingIndex, upgrade as billingUpgrade } from '@/routes/setting/billing';
import { type NavItem } from '@/types';

const page = usePage();
const auth = computed(() => page.props.auth);
const usage = computed(() => (page.props as any).usage);

const navItems: NavItem[] = [
    {
        title: 'Analytics',
        href: analyticsIndex().url,
        icon: IconChartBar,
        activePattern: '/analytics',
    },
    {
        title: 'Links',
        href: linksIndex().url,
        icon: IconClick,
        activePattern: '/links',
    },
    {
        title: 'Events',
        href: eventsIndex().url,
        icon: IconCalendarEvent,
        activePattern: '/events',
    },
    {
        title: 'Settings',
        href: settingsAccount().url,
        icon: IconSettings,
        activePattern: '/settings',
    },
];

const switchWorkspace = (workspaceId: string) => {
    router.put(
        workspacesUpdateCurrent().url,
        { workspace_id: workspaceId },
        { preserveScroll: true },
    );
};
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <SidebarMenuButton
                                size="lg"
                                class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
                            >
                                <Avatar
                                    :src="auth.user?.current_workspace?.logo_url"
                                    :name="auth.user?.current_workspace?.name ?? '?'"
                                    class="h-8 w-8 shrink-0 rounded-lg"
                                    fallback-class="bg-sidebar-accent text-sidebar-accent-foreground"
                                />
                                <div class="grid flex-1 text-left text-sm leading-tight">
                                    <span class="truncate font-semibold">
                                        {{ auth.user?.current_workspace?.name ?? 'Select workspace' }}
                                    </span>
                                </div>
                                <IconChevronRight class="ml-auto size-4" />
                            </SidebarMenuButton>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent
                            class="w-[--reka-dropdown-menu-trigger-width] min-w-56 rounded-lg"
                            align="start"
                            side="right"
                            :side-offset="4"
                        >
                            <DropdownMenuLabel class="text-xs text-muted-foreground">
                                Workspaces
                            </DropdownMenuLabel>
                            <div class="space-y-0.5">
                                <DropdownMenuItem
                                    v-for="workspace in auth.user?.workspaces"
                                    :key="workspace.id"
                                    class="gap-2"
                                    :class="workspace.id === auth.user?.current_workspace_id ? 'bg-accent' : ''"
                                    @click="switchWorkspace(workspace.id)"
                                >
                                    <Avatar
                                        :src="workspace.logo_url"
                                        :name="workspace.name"
                                        class="h-5 w-5 shrink-0 rounded-md"
                                        fallback-class="text-[10px]"
                                    />
                                    {{ workspace.name }}
                                </DropdownMenuItem>
                            </div>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem as-child>
                                <Link :href="workspacesCreate().url">
                                    <IconPlus class="size-4" />
                                    Create Workspace
                                </Link>
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="navItems" />
        </SidebarContent>

        <div v-if="usage" class="px-3 py-3 group-data-[collapsible=icon]:hidden">
            <Link
                :href="billingIndex().url"
                class="flex items-center gap-x-0.5 text-sidebar-foreground/60 hover:text-sidebar-foreground transition-colors"
            >
                <span class="text-xs">Usage</span>
                <IconChevronRight class="size-3.5" />
            </Link>

            <div class="mt-3 flex flex-col gap-3">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-1.5">
                        <div class="flex items-center gap-1.5">
                            <IconLink class="size-3.5 text-sidebar-foreground/60" />
                            <span class="text-xs font-medium text-sidebar-foreground/60">Links</span>
                        </div>
                        <span class="text-xs font-medium text-sidebar-foreground/60">{{ usage.links.used }} of {{ usage.links.limit }}</span>
                    </div>
                    <div class="overflow-hidden rounded-full bg-sidebar-accent">
                        <div
                            class="h-1 rounded-full bg-gradient-to-r from-violet-400 to-violet-600 transition-all"
                            :style="{ width: `${usage.links.percent}%` }"
                        />
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between gap-2 mb-1.5">
                        <div class="flex items-center gap-1.5">
                            <IconClick class="size-3.5 text-sidebar-foreground/60" />
                            <span class="text-xs font-medium text-sidebar-foreground/60">Events</span>
                        </div>
                        <span class="text-xs font-medium text-sidebar-foreground/60">{{ usage.events.used }} of {{ usage.events.limit }}</span>
                    </div>
                    <div class="overflow-hidden rounded-full bg-sidebar-accent">
                        <div
                            class="h-1 rounded-full bg-gradient-to-r from-violet-400 to-violet-600 transition-all"
                            :style="{ width: `${usage.events.percent}%` }"
                        />
                    </div>
                </div>
            </div>

            <div class="my-3 text-center text-xs text-sidebar-foreground/50">
                Usage will reset {{ usage.next_reset }}
            </div>

            <Link
                :href="billingUpgrade().url"
                class="flex w-full items-center justify-center rounded-md bg-primary px-3 py-1.5 text-xs font-medium text-primary-foreground hover:bg-primary/90 transition-colors"
            >
                Upgrade
            </Link>
        </div>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
