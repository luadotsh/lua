<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    IconCalendarEvent,
    IconChartBar,
    IconChevronRight,
    IconClick,
    IconKey,
    IconLink,
    IconPlugConnected,
    IconTag,
    IconUsers,
    IconWorld,
} from '@tabler/icons-vue';
import { computed } from 'vue';

import NavMain from '@/components/NavMain.vue';
import { Avatar } from '@/components/ui/avatar';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    Sidebar,
    SidebarContent,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import WorkspaceMenuContent from '@/components/WorkspaceMenuContent.vue';
import { formatNumber } from '@/lib/metrics';
import { index as analyticsIndex } from '@/routes/analytics';
import { index as eventsIndex } from '@/routes/events';
import { index as linksIndex } from '@/routes/links';
import { index as apiTokensIndex } from '@/routes/setting/api-tokens';
import {
    index as billingIndex,
    upgrade as billingUpgrade,
} from '@/routes/setting/billing';
import { index as domainsIndex } from '@/routes/setting/domains';
import { index as mcpIndex } from '@/routes/setting/mcp';
import { index as tagsIndex } from '@/routes/setting/tags';
import { index as teamMembersIndex } from '@/routes/setting/team-members';
import { type NavItem } from '@/types';

const page = usePage();
const auth = computed(() => page.props.auth);
const usage = computed(() => (page.props as any).usage);

const navItems: NavItem[] = [
    {
        title: 'Analytics',
        href: analyticsIndex().url,
        icon: IconChartBar,
    },
    {
        title: 'Links',
        href: linksIndex().url,
        icon: IconClick,
    },
    {
        title: 'Events',
        href: eventsIndex().url,
        icon: IconCalendarEvent,
    },
];

// Workspace-level configuration is a place you go to, not something buried two
// clicks deep: it lives in the sidebar. What is personal to the account —
// profile, sign-in, billing — stays behind the user menu at the bottom.
const workspaceNavItems: NavItem[] = [
    {
        title: 'Domains',
        href: domainsIndex().url,
        icon: IconWorld,
    },
    {
        title: 'Tags',
        href: tagsIndex().url,
        icon: IconTag,
    },
    {
        title: 'Members',
        href: teamMembersIndex().url,
        icon: IconUsers,
    },
    {
        title: 'API Tokens',
        href: apiTokensIndex().url,
        icon: IconKey,
    },
    {
        title: 'MCP',
        href: mcpIndex().url,
        icon: IconPlugConnected,
    },
];
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
                                    :src="
                                        auth.user?.current_workspace?.logo_url
                                    "
                                    :name="
                                        auth.user?.current_workspace?.name ??
                                        '?'
                                    "
                                    class="h-8 w-8 shrink-0 rounded-lg"
                                    fallback-class="bg-violet-100 font-bold text-violet-700"
                                />
                                <div
                                    class="grid flex-1 text-left text-sm leading-tight"
                                >
                                    <span class="truncate font-semibold">
                                        {{
                                            auth.user?.current_workspace
                                                ?.name ?? 'Select workspace'
                                        }}
                                    </span>
                                </div>
                                <IconChevronRight class="ml-auto size-4" />
                            </SidebarMenuButton>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent
                            class="w-[--reka-dropdown-menu-trigger-width] min-w-64 rounded-lg"
                            align="start"
                            side="right"
                            :side-offset="4"
                        >
                            <WorkspaceMenuContent />
                        </DropdownMenuContent>
                    </DropdownMenu>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="navItems" />
            <NavMain :items="workspaceNavItems" label="Workspace" />
        </SidebarContent>

        <div
            v-if="usage"
            class="px-3 py-3 group-data-[collapsible=icon]:hidden"
        >
            <Link
                :href="billingIndex().url"
                class="flex items-center gap-x-0.5 text-sidebar-foreground/60 transition-colors hover:text-sidebar-foreground"
            >
                <span class="text-xs">Usage</span>
                <IconChevronRight class="size-3.5" />
            </Link>

            <div class="mt-3 flex flex-col gap-3">
                <div>
                    <div class="mb-1.5 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5">
                            <IconLink
                                class="size-3.5 text-sidebar-foreground/60"
                            />
                            <span
                                class="text-xs font-medium text-sidebar-foreground/60"
                                >Links</span
                            >
                        </div>
                        <span
                            class="text-xs font-medium text-sidebar-foreground/60"
                            >{{ formatNumber(usage.links.used) }} of
                            {{ formatNumber(usage.links.limit) }}</span
                        >
                    </div>
                    <div class="overflow-hidden rounded-full bg-sidebar-accent">
                        <div
                            class="h-1 rounded-full bg-gradient-to-r from-violet-400 to-violet-600 transition-all"
                            :style="{ width: `${usage.links.percent}%` }"
                        />
                    </div>
                </div>

                <div>
                    <div class="mb-1.5 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5">
                            <IconClick
                                class="size-3.5 text-sidebar-foreground/60"
                            />
                            <span
                                class="text-xs font-medium text-sidebar-foreground/60"
                                >Events</span
                            >
                        </div>
                        <span
                            class="text-xs font-medium text-sidebar-foreground/60"
                            >{{ formatNumber(usage.events.used) }} of
                            {{ formatNumber(usage.events.limit) }}</span
                        >
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
                class="flex w-full items-center justify-center rounded-md bg-primary px-3 py-1.5 text-xs font-medium text-primary-foreground transition-colors hover:bg-primary/90"
            >
                Upgrade
            </Link>
        </div>
    </Sidebar>
    <slot />
</template>
