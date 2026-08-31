<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { IconArrowLeft } from '@tabler/icons-vue';
import JsonLd from '@/components/site/JsonLd.vue';
import Seo from '@/components/site/Seo.vue';
import SiteLayout from '@/layouts/site/SiteLayout.vue';
import site from '@/routes/site';

defineProps<{
    slug: string;
    entry: { term: string; short: string; body: string[]; related: string[] };
    related: Array<{ slug: string; term: string; short: string }>;
    seo: { title: string; description: string };
}>();
</script>

<template>
    <Seo :title="seo.title" :description="seo.description" />
    <JsonLd
        :data="{
            '@type': 'DefinedTerm',
            name: entry.term,
            description: entry.short,
            inDefinedTermSet: { '@type': 'DefinedTermSet', name: 'Lua glossary' },
        }"
    />

    <SiteLayout>
        <article class="mx-auto max-w-3xl px-6 py-16 sm:px-10 sm:py-24">
            <Link
                :href="site.glossary.index.url()"
                data-testid="back-to-glossary"
                class="inline-flex items-center gap-1.5 text-sm text-muted-foreground transition-colors hover:text-foreground"
            >
                <IconArrowLeft class="size-4" />
                Glossary
            </Link>

            <header class="mt-8">
                <h1 class="font-display text-4xl font-semibold tracking-tight text-balance sm:text-5xl">
                    {{ entry.term }}
                </h1>
                <!--
                    The one-sentence definition is the answer somebody arrived
                    for, so it sits above the explanation rather than inside it.
                -->
                <p class="mt-6 border-l-2 border-primary/40 pl-6 text-lg leading-relaxed">
                    {{ entry.short }}
                </p>
            </header>

            <div class="mt-12 space-y-6">
                <p
                    v-for="(paragraph, index) in entry.body"
                    :key="index"
                    class="leading-relaxed text-muted-foreground"
                >
                    {{ paragraph }}
                </p>
            </div>

            <nav v-if="related.length" class="mt-16 border-t border-border pt-8">
                <h2 class="label">Related</h2>
                <dl class="mt-4 space-y-4">
                    <div v-for="item in related" :key="item.slug">
                        <dt>
                            <Link
                                :href="site.glossary.show.url(item.slug)"
                                class="font-medium underline-offset-4 hover:underline"
                            >
                                {{ item.term }}
                            </Link>
                        </dt>
                        <dd class="mt-1 text-sm text-muted-foreground">{{ item.short }}</dd>
                    </div>
                </dl>
            </nav>
        </article>
    </SiteLayout>
</template>
