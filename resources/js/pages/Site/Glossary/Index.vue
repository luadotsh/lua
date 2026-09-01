<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

import JsonLd from '@/components/site/JsonLd.vue';
import PageHeader from '@/components/site/PageHeader.vue';
import Seo from '@/components/site/Seo.vue';
import SiteLayout from '@/layouts/site/SiteLayout.vue';
import site from '@/routes/site';

type Term = { slug: string; term: string; short: string };

defineProps<{
    letters: Array<{ letter: string; terms: Term[] }>;
    seo: { title: string; description: string };
}>();
</script>

<template>
    <Seo :title="seo.title" :description="seo.description" />
    <JsonLd
        :data="{
            '@type': 'CollectionPage',
            name: seo.title,
            description: seo.description,
        }"
    />

    <SiteLayout>
        <section class="px-6 py-16 sm:px-10 sm:py-24">
            <PageHeader
                eyebrow="Glossary"
                title="The words behind a click"
                lead="Defined by how the web actually behaves, including the parts that make the numbers less certain than they look."
            />

            <!-- Letter jump. Short list, so it is one row rather than a rail. -->
            <nav class="mt-10 flex flex-wrap gap-2" aria-label="Jump to letter">
                <a
                    v-for="group in letters"
                    :key="group.letter"
                    :href="`#letter-${group.letter}`"
                    class="inline-flex size-8 items-center justify-center rounded-md border border-border font-mono text-sm text-muted-foreground transition-colors hover:border-foreground/30 hover:text-foreground"
                >
                    {{ group.letter }}
                </a>
            </nav>

            <div class="mt-14 space-y-14">
                <section
                    v-for="group in letters"
                    :id="`letter-${group.letter}`"
                    :key="group.letter"
                    class="scroll-mt-24 lg:grid lg:grid-cols-[6rem_minmax(0,1fr)] lg:gap-8"
                >
                    <h2
                        class="font-display text-3xl font-semibold text-primary-text"
                    >
                        {{ group.letter }}
                    </h2>

                    <dl
                        class="mt-6 divide-y divide-border border-t border-border lg:mt-0 lg:border-t-0"
                    >
                        <div
                            v-for="term in group.terms"
                            :key="term.slug"
                            class="py-5 first:lg:pt-0"
                        >
                            <dt>
                                <Link
                                    :href="site.glossary.show.url(term.slug)"
                                    :data-testid="`term-${term.slug}`"
                                    class="font-medium underline-offset-4 hover:underline"
                                >
                                    {{ term.term }}
                                </Link>
                            </dt>
                            <dd
                                class="mt-1.5 text-sm leading-relaxed text-muted-foreground"
                            >
                                {{ term.short }}
                            </dd>
                        </div>
                    </dl>
                </section>
            </div>
        </section>
    </SiteLayout>
</template>
