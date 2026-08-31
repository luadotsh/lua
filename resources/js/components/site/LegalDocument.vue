<script setup lang="ts">
import JsonLd from '@/components/site/JsonLd.vue';
import Seo from '@/components/site/Seo.vue';
import SiteLayout from '@/layouts/site/SiteLayout.vue';

/**
 * Terms and Privacy are the same page with different words, so they share one
 * component and differ only by the `sections` array each passes in.
 */
export type LegalSection = {
    id: string;
    title: string;
    paragraphs?: string[];
    bullets?: string[];
};

defineProps<{
    title: string;
    lastUpdated: string;
    lead: string;
    sections: LegalSection[];
    seo: { title: string; description: string };
}>();
</script>

<template>
    <Seo :title="seo.title" :description="seo.description" />
    <JsonLd :data="{ '@type': 'WebPage', name: seo.title, description: seo.description }" />

    <SiteLayout>
        <div class="px-6 py-16 sm:px-10 sm:py-24">
            <header class="max-w-3xl">
                <p class="label">Legal</p>
                <h1 class="mt-2 font-display text-4xl font-semibold tracking-tight text-balance sm:text-5xl">
                    {{ title }}
                </h1>
                <p class="mt-3 text-sm text-muted-foreground">
                    Last updated {{ lastUpdated }}
                </p>
                <p class="mt-6 text-lg text-muted-foreground">{{ lead }}</p>
            </header>

            <div class="mt-14 lg:grid lg:grid-cols-[14rem_minmax(0,1fr)] lg:gap-14">
                <!-- Sticky contents on wide screens; on a phone the document
                     reads straight through and the nav would only be in the way. -->
                <aside class="hidden lg:block">
                    <nav class="sticky top-24 space-y-2 border-l border-border pl-4 text-sm">
                        <a
                            v-for="section in sections"
                            :key="section.id"
                            :href="`#${section.id}`"
                            class="block text-muted-foreground transition-colors hover:text-foreground"
                        >
                            {{ section.title }}
                        </a>
                    </nav>
                </aside>

                <div class="max-w-3xl min-w-0 space-y-12">
                    <section v-for="section in sections" :key="section.id" :id="section.id">
                        <h2 class="scroll-mt-24 text-xl font-semibold tracking-tight">
                            {{ section.title }}
                        </h2>
                        <p
                            v-for="(paragraph, index) in section.paragraphs ?? []"
                            :key="index"
                            class="mt-4 leading-relaxed text-muted-foreground"
                        >
                            {{ paragraph }}
                        </p>
                        <ul
                            v-if="section.bullets"
                            class="mt-4 list-disc space-y-2 pl-5 leading-relaxed text-muted-foreground"
                        >
                            <li v-for="item in section.bullets" :key="item">{{ item }}</li>
                        </ul>
                    </section>
                </div>
            </div>
        </div>
    </SiteLayout>
</template>
