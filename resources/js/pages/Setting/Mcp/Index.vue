<script setup lang="ts">
import { Head, usePoll } from "@inertiajs/vue3";
import { IconExternalLink, IconPlugConnected } from "@tabler/icons-vue";
import { ref } from "vue";
import ConfirmDeleteModal from "@/components/ConfirmDeleteModal.vue";
import HeadingSmall from "@/components/HeadingSmall.vue";
import McpAdvancedClients from "@/components/mcp/McpAdvancedClients.vue";
import McpPrimarySetup from "@/components/mcp/McpPrimarySetup.vue";
import { Button } from "@/components/ui/button";
import date from "@/date";
import AppLayout from "@/layouts/AppLayout.vue";
import { destroy as mcpDisconnect } from "@/routes/setting/mcp";

interface ConnectedClient {
    id: string;
    name: string;
    last_used_at: string | null;
}

defineProps<{
    mcpUrl: string;
    connectedClients: ConnectedClient[];
    docsUrl: string;
}>();

const deleteModal = ref<InstanceType<typeof ConfirmDeleteModal> | null>(null);

// The connection only appears once the client finishes the OAuth handshake in
// another window, so the list has to come to us rather than wait for a reload.
usePoll(1000, {
    only: ["connectedClients"],
});

const confirmDisconnect = (client: ConnectedClient): void => {
    deleteModal.value?.open({
        url: mcpDisconnect.url(client.id),
        confirmText: client.name,
    });
};
</script>

<template>
    <Head title="MCP" />

    <AppLayout title="MCP">
        <div class="p-4 sm:p-6">
            <div class="flex max-w-3xl flex-col gap-10">
                <p class="text-sm text-foreground/70">
                    Connect AI assistants to this workspace. Connections are yours alone,
                    and each one can only act on the workspace it was authorised from.
                </p>

                <section class="space-y-6">
                    <McpPrimarySetup :mcp-url="mcpUrl" />
                    <McpAdvancedClients :mcp-url="mcpUrl" />
                </section>

                <section class="space-y-4">
                    <HeadingSmall
                        title="Connected apps"
                        description="Assistants you've signed in with. You can disconnect any you no longer use."
                    />

                    <div
                        v-if="connectedClients.length === 0"
                        class="rounded-xl border border-dashed border-border bg-card/40 px-4 py-6 text-center text-sm font-medium text-foreground/60"
                        data-testid="mcp-connected-empty"
                    >
                        Nothing connected yet. Use Claude, ChatGPT, or another client above.
                    </div>

                    <div v-else class="space-y-3">
                        <div
                            v-for="client in connectedClients"
                            :key="client.id"
                            class="flex items-center gap-4 rounded-xl border border-border bg-card p-4 shadow-2xs"
                            :data-testid="`mcp-connected-client-${client.id}`"
                        >
                            <div
                                class="inline-flex size-10 -rotate-2 flex-shrink-0 items-center justify-center rounded-2xl border border-border bg-violet-100 shadow-2xs"
                            >
                                <IconPlugConnected class="size-5 text-zinc-900" stroke-width="2" />
                            </div>
                            <div class="min-w-0 flex-1 space-y-0.5">
                                <div class="truncate text-sm font-bold text-foreground">
                                    {{ client.name }}
                                </div>
                                <div class="flex items-center gap-1.5 text-xs font-medium text-foreground/60">
                                    <span class="relative flex size-2">
                                        <span
                                            class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400/60"
                                        />
                                        <span
                                            class="relative inline-flex size-2 rounded-full bg-emerald-500"
                                        />
                                    </span>
                                    <span>
                                        Last used:
                                        {{
                                            client.last_used_at
                                                ? date.diffForHumans(client.last_used_at)
                                                : "Never"
                                        }}
                                    </span>
                                </div>
                            </div>
                            <Button
                                variant="outline"
                                size="sm"
                                class="shrink-0"
                                @click="confirmDisconnect(client)"
                            >
                                Disconnect
                            </Button>
                        </div>
                    </div>
                </section>

                <section class="space-y-4">
                    <HeadingSmall
                        title="Documentation"
                        description="Client setup guides, available tools, and troubleshooting."
                    />
                    <Button as="a" variant="outline" size="sm" target="_blank" :href="docsUrl">
                        <IconExternalLink class="size-4" />
                        View docs
                    </Button>
                </section>
            </div>
        </div>

        <ConfirmDeleteModal
            ref="deleteModal"
            method="delete"
            title="Disconnect app"
            description="This signs the app out of Lua. It will need to reconnect before it can use MCP again."
            action="Disconnect"
        />
    </AppLayout>
</template>
