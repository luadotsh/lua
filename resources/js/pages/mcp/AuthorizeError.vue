<script setup lang="ts">
import AuthorizeLayout from '@/layouts/mcp/AuthorizeLayout.vue';

defineProps<{
    error: string;
    errorDescription: string;
}>();
</script>

<template>
    <AuthorizeLayout
        page-title="Authorization failed"
        title="Could not connect"
        description="This authorization request is invalid or expired. Close this window and try connecting again from your MCP client."
    >
        <p class="text-sm text-muted-foreground">
            {{ errorDescription }}
        </p>

        <p
            class="rounded-lg border border-border bg-muted px-3 py-2 font-mono text-xs text-foreground"
        >
            Error: {{ error }}
        </p>
    </AuthorizeLayout>
</template>
