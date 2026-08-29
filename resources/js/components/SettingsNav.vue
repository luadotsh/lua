<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { edit as accountEdit } from '@/routes/setting/account';
import { index as apiTokensIndex } from '@/routes/setting/api-tokens';
import { index as billingIndex } from '@/routes/setting/billing';
import { index as domainsIndex } from '@/routes/setting/domains';
import { index as tagsIndex } from '@/routes/setting/tags';
import { index as teamMembersIndex } from '@/routes/setting/team-members';
import { edit as workspaceEdit } from '@/routes/setting/workspace';

const navItems = [
    { title: 'Account', href: accountEdit().url, pattern: '/settings/account' },
    { title: 'Workspace', href: workspaceEdit().url, pattern: '/settings/workspace' },
    { title: 'Members', href: teamMembersIndex().url, pattern: '/settings/team-members' },
    { title: 'Domains', href: domainsIndex().url, pattern: '/settings/domains' },
    { title: 'Tags', href: tagsIndex().url, pattern: '/settings/tags' },
    { title: 'API Tokens', href: apiTokensIndex().url, pattern: '/settings/api-tokens' },
    { title: 'Billing', href: billingIndex().url, pattern: '/settings/billing' },
];

const { isCurrentUrl } = useCurrentUrl();
</script>

<template>
    <nav
        class="inline-flex h-9 w-fit items-center justify-center rounded-lg bg-muted p-1 text-muted-foreground"
    >
        <Link
            v-for="item in navItems"
            :key="item.href"
            :href="item.href"
            class="inline-flex items-center justify-center rounded-md px-3 py-1 text-sm font-medium whitespace-nowrap transition-all focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:pointer-events-none disabled:opacity-50"
            :class="
                isCurrentUrl(item.pattern)
                    ? 'bg-background text-foreground shadow-sm'
                    : 'hover:bg-background/50 hover:text-foreground'
            "
        >
            {{ item.title }}
        </Link>
    </nav>
</template>
