<script setup lang="ts">
import { IconCheck, IconCopy } from '@tabler/icons-vue';
import { computed, ref } from 'vue';
import JsonLd from '@/components/site/JsonLd.vue';
import PageHeader from '@/components/site/PageHeader.vue';
import Seo from '@/components/site/Seo.vue';
import SiteLayout from '@/layouts/site/SiteLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

defineProps<{ seo: { title: string; description: string } }>();

const url = ref('https://example.com/spring');
const fields = ref<Record<string, string>>({
    utm_source: 'newsletter',
    utm_medium: 'email',
    utm_campaign: 'spring-2026',
    utm_term: '',
    utm_content: '',
});

const hints: Record<string, string> = {
    utm_source: 'Where the link was published: newsletter, instagram, a partner\'s name.',
    utm_medium: 'How it travelled: email, social, cpc, qr, print.',
    utm_campaign: 'The campaign this belongs to. Keep it identical across placements.',
    utm_term: 'Paid keyword, if there is one. Usually left empty.',
    utm_content: 'Which of two links in the same place. The header button, or the footer one.',
};

const built = computed(() => {
    let base: URL;

    try {
        base = new URL(url.value);
    } catch {
        return '';
    }

    for (const [key, value] of Object.entries(fields.value)) {
        const trimmed = value.trim();

        if (trimmed === '') {
            base.searchParams.delete(key);
        } else {
            base.searchParams.set(key, trimmed);
        }
    }

    return base.toString();
});

const invalid = computed(() => url.value.trim() !== '' && built.value === '');

const copied = ref(false);

const copy = async (): Promise<void> => {
    if (built.value === '') {
        return;
    }

    await navigator.clipboard.writeText(built.value);
    copied.value = true;
    window.setTimeout(() => (copied.value = false), 1600);
};
</script>

<template>
    <Seo :title="seo.title" :description="seo.description" />
    <JsonLd
        :data="{
            '@type': 'WebApplication',
            name: 'UTM builder',
            applicationCategory: 'UtilitiesApplication',
            offers: { '@type': 'Offer', price: '0', priceCurrency: 'USD' },
        }"
    />

    <SiteLayout>
        <section class="mx-auto max-w-4xl px-6 py-16 sm:px-10 sm:py-24">
            <PageHeader
                eyebrow="Tool"
                title="UTM builder"
                lead="Tag a link before you publish it, so the click reads back as a placement instead of as Direct. This runs in your browser; the URL is never sent to us."
            />

            <div class="mt-12 space-y-6">
                <div>
                    <Label for="destination">Destination URL</Label>
                    <Input
                        id="destination"
                        v-model="url"
                        data-testid="utm-url"
                        class="mt-2 font-mono"
                        placeholder="https://example.com/page"
                    />
                    <p v-if="invalid" class="mt-2 text-sm text-destructive">
                        That is not a URL yet. It needs the https:// too.
                    </p>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div v-for="(hint, key) in hints" :key="key">
                        <Label :for="key" class="font-mono text-xs">{{ key }}</Label>
                        <Input
                            :id="key"
                            v-model="fields[key]"
                            :data-testid="`utm-${key}`"
                            class="mt-2"
                        />
                        <p class="mt-2 text-xs leading-relaxed text-muted-foreground">{{ hint }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-10 site-card p-6">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-sm font-medium">Your tagged link</h2>
                    <Button
                        size="sm"
                        variant="outline"
                        data-testid="utm-copy"
                        :disabled="built === ''"
                        @click="copy"
                    >
                        <IconCheck v-if="copied" class="size-4" />
                        <IconCopy v-else class="size-4" />
                        {{ copied ? 'Copied' : 'Copy' }}
                    </Button>
                </div>
                <p
                    data-testid="utm-result"
                    class="mt-4 font-mono text-sm break-all text-muted-foreground"
                >
                    {{ built || 'Enter a destination URL above.' }}
                </p>
            </div>

            <div class="mt-16 border-t border-border pt-10">
                <h2 class="font-display text-2xl font-semibold tracking-tight">
                    Why bother tagging at all
                </h2>
                <p class="mt-4 leading-relaxed text-muted-foreground">
                    Because the referrer will not tell you. A link opened inside
                    an app sends nothing, most sites send only their domain
                    rather than the page, and anything typed or scanned has no
                    referring page by definition. A large share of clicks
                    therefore arrive as "Direct", which is not a source but the
                    absence of one. UTM parameters travel in the URL, where no
                    privacy setting strips them, and they survive being pasted
                    into a chat window.
                </p>
            </div>
        </section>
    </SiteLayout>
</template>
