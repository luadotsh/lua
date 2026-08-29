<script setup lang="ts">
import { Head, Link, useForm, usePage } from "@inertiajs/vue3";
import { IconCheck, IconChevronDown } from "@tabler/icons-vue";
import { computed, ref } from "vue";
import { Button } from "@/components/ui/button";
import {
    Combobox,
    ComboboxAnchor,
    ComboboxEmpty,
    ComboboxInput,
    ComboboxItem,
    ComboboxItemIndicator,
    ComboboxList,
    ComboboxTrigger,
    ComboboxViewport,
} from "@/components/ui/combobox";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import AppLayout from "@/layouts/AppLayout.vue";
import * as linksRoute from "@/routes/links";
import type { BreadcrumbItem } from "@/types";

/**
 * Creating a link asks for the three things that make one: where it points, on
 * which domain, and optionally what to call it. Everything else — tags, UTMs,
 * platform targeting, password, expiry — is configuration you tune afterwards,
 * so it lives on the edit screen and the store redirects there.
 */
const domains = usePage().props.domains as string[];

const form = useForm({
    url: "",
    domain: domains[0] ?? "",
    key: "",
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Links", href: linksRoute.index.url() },
    { title: "New link" },
];

const domainSearch = ref("");

const filteredDomains = computed(() =>
    domains.filter((d) => d.toLowerCase().includes(domainSearch.value.toLowerCase())),
);

const store = () => form.post(linksRoute.store.url());
</script>

<template>
    <Head title="New link" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-xl p-4 sm:p-6">
            <form class="flex flex-col gap-5" @submit.prevent="store">
                <div class="grid gap-2">
                    <Label for="url">
                        Destination URL <span class="text-destructive">*</span>
                    </Label>
                    <Input
                        id="url"
                        v-model="form.url"
                        type="text"
                        autofocus
                        placeholder="e.g. https://example.com"
                    />
                    <p v-if="form.errors.url" class="text-sm text-destructive">
                        {{ form.errors.url }}
                    </p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label>Short Link <span class="text-destructive">*</span></Label>
                        <Combobox
                            :model-value="form.domain"
                            @update:model-value="(v) => (form.domain = v as string)"
                            @update:open="(o) => { if (o) domainSearch = '' }"
                        >
                            <ComboboxAnchor as-child>
                                <ComboboxTrigger as-child>
                                    <Button variant="outline" class="w-full justify-between font-normal">
                                        <span :class="!form.domain ? 'text-muted-foreground' : ''">
                                            {{ form.domain || "Select domain..." }}
                                        </span>
                                        <IconChevronDown class="h-4 w-4 shrink-0 opacity-50" />
                                    </Button>
                                </ComboboxTrigger>
                            </ComboboxAnchor>
                            <ComboboxList align="start" class="w-[var(--reka-combobox-trigger-width)]">
                                <ComboboxInput
                                    v-model="domainSearch"
                                    :display-value="() => domainSearch"
                                    placeholder="Search domain..."
                                    auto-focus
                                />
                                <ComboboxViewport class="p-1">
                                    <ComboboxEmpty class="py-3 text-center text-sm text-muted-foreground">
                                        No domains found.
                                    </ComboboxEmpty>
                                    <ComboboxItem v-for="d in filteredDomains" :key="d" :value="d">
                                        {{ d }}
                                        <ComboboxItemIndicator class="ml-auto">
                                            <IconCheck class="h-4 w-4" />
                                        </ComboboxItemIndicator>
                                    </ComboboxItem>
                                </ComboboxViewport>
                            </ComboboxList>
                        </Combobox>
                        <p v-if="form.errors.domain" class="text-sm text-destructive">
                            {{ form.errors.domain }}
                        </p>
                    </div>

                    <div class="grid gap-2">
                        <Label for="key">Custom back-half (optional)</Label>
                        <Input
                            id="key"
                            v-model="form.key"
                            type="text"
                            placeholder="e.g. super-link"
                        />
                        <p v-if="form.errors.key" class="text-sm text-destructive">
                            {{ form.errors.key }}
                        </p>
                    </div>
                </div>

                <p class="text-sm text-muted-foreground">
                    Tags, UTMs, platform targeting, a password and an expiry come next.
                </p>

                <div class="flex items-center gap-2">
                    <Button type="submit" :disabled="form.processing">Continue</Button>
                    <Button variant="ghost" as-child>
                        <Link :href="linksRoute.index.url()">Cancel</Link>
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
