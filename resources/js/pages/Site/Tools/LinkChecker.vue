<script setup lang="ts">
import { IconArrowNarrowRight, IconLoader2 } from '@tabler/icons-vue';
import { ref } from 'vue';

import JsonLd from '@/components/site/JsonLd.vue';
import PageHeader from '@/components/site/PageHeader.vue';
import Seo from '@/components/site/Seo.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import SiteLayout from '@/layouts/site/SiteLayout.vue';
import site from '@/routes/site';

defineProps<{ seo: { title: string; description: string } }>();

type Hop = { url: string; status: number | null; error: string | null };

const url = ref('');
const hops = ref<Hop[]>([]);
const destination = ref<string | null>(null);
const error = ref<string | null>(null);
const loading = ref(false);

const check = async (): Promise<void> => {
    if (url.value.trim() === '' || loading.value) {
        return;
    }

    loading.value = true;
    hops.value = [];
    destination.value = null;
    error.value = null;

    try {
        const response = await window.axios.post(site.tools.check.url(), {
            url: url.value.trim(),
        });

        hops.value = response.data.hops;
        destination.value = response.data.destination;
        error.value = response.data.error;
    } catch (thrown: unknown) {
        const status = (thrown as { response?: { status?: number } }).response
            ?.status;

        error.value =
            status === 429
                ? 'Too many checks. Give it a minute.'
                : status === 422
                  ? 'That does not look like a web address.'
                  : 'Something went wrong on our side.';
    } finally {
        loading.value = false;
    }
};

const tone = (status: number | null): string => {
    if (status === null) {
        return 'text-destructive';
    }

    if (status >= 300 && status < 400) {
        return 'text-primary-text';
    }

    return status >= 400 ? 'text-destructive' : 'text-emerald-600';
};
</script>

<template>
    <Seo :title="seo.title" :description="seo.description" />
    <JsonLd
        :data="{
            '@type': 'WebApplication',
            name: 'Redirect checker',
            applicationCategory: 'UtilitiesApplication',
            offers: { '@type': 'Offer', price: '0', priceCurrency: 'USD' },
        }"
    />

    <SiteLayout>
        <section class="mx-auto max-w-4xl px-6 py-16 sm:px-10 sm:py-24">
            <PageHeader
                eyebrow="Tool"
                title="Redirect checker"
                lead="Follow a short link through every hop and see where it really ends up, with the status each step returned."
            />

            <form class="mt-12" @submit.prevent="check">
                <Label for="check-url">Short link</Label>
                <div class="mt-2 flex flex-col gap-3 sm:flex-row">
                    <Input
                        id="check-url"
                        v-model="url"
                        data-testid="check-url"
                        class="font-mono"
                        placeholder="https://example.com/abc"
                    />
                    <Button
                        type="submit"
                        data-testid="check-submit"
                        :disabled="loading"
                    >
                        <IconLoader2
                            v-if="loading"
                            class="size-4 animate-spin"
                        />
                        {{ loading ? 'Following' : 'Follow it' }}
                    </Button>
                </div>
            </form>

            <p
                v-if="error"
                data-testid="check-error"
                class="mt-6 text-sm text-destructive"
            >
                {{ error }}
            </p>

            <ol
                v-if="hops.length"
                data-testid="check-hops"
                class="mt-10 space-y-px"
            >
                <li
                    v-for="(hop, index) in hops"
                    :key="index"
                    class="flex items-start gap-4 rounded-lg border border-border bg-card p-4"
                >
                    <span
                        class="font-mono text-xs text-muted-foreground tabular-nums"
                    >
                        {{ String(index + 1).padStart(2, '0') }}
                    </span>
                    <span class="min-w-0 flex-1 font-mono text-sm break-all">{{
                        hop.url
                    }}</span>
                    <span
                        class="font-mono text-sm tabular-nums"
                        :class="tone(hop.status)"
                    >
                        {{ hop.status ?? '—' }}
                    </span>
                </li>
            </ol>

            <p
                v-if="destination"
                data-testid="check-destination"
                class="mt-6 flex items-start gap-2 text-sm"
            >
                <IconArrowNarrowRight
                    class="mt-0.5 size-4 shrink-0 text-muted-foreground"
                />
                <span>
                    Ends at
                    <span class="font-mono break-all">{{ destination }}</span>
                </span>
            </p>

            <div class="mt-16 border-t border-border pt-10">
                <h2 class="font-display text-2xl font-semibold tracking-tight">
                    What this will not do
                </h2>
                <p class="mt-4 leading-relaxed text-muted-foreground">
                    It only fetches public addresses. Anything resolving to a
                    private network is refused, on every hop, because a public
                    URL is free to redirect somewhere internal and this runs on
                    our server rather than yours. It reads the status and the
                    location and never the page body, it gives up after ten
                    hops, and it is rate limited. It is a diagnostic, not a
                    scanner: it cannot tell you whether the destination is safe,
                    only where it is.
                </p>
            </div>
        </section>
    </SiteLayout>
</template>
