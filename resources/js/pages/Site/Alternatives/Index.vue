<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { IconArrowRight } from '@tabler/icons-vue';

import JsonLd from '@/components/site/JsonLd.vue';
import Seo from '@/components/site/Seo.vue';
import SiteLayout from '@/layouts/site/SiteLayout.vue';
import site from '@/routes/site';

defineProps<{
    alternatives: Array<{ slug: string; name: string; intro: string }>;
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
            <p class="label">Alternatives</p>
            <h1
                class="mt-2 max-w-2xl font-display text-4xl font-semibold tracking-tight text-balance sm:text-5xl"
            >
                How Lua compares
            </h1>
            <p class="mt-4 max-w-2xl text-lg text-muted-foreground">
                An honest read on each one: what they do better, what we do
                better, and who should stay where they are.
            </p>

            <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="alternative in alternatives"
                    :key="alternative.slug"
                    :href="site.alternatives.show.url(alternative.slug)"
                    :data-testid="`alternative-${alternative.slug}`"
                    class="group site-card flex flex-col p-6 transition-colors hover:border-foreground/20"
                >
                    <p
                        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
                    >
                        Lua vs
                    </p>
                    <h2 class="mt-1 text-2xl font-semibold tracking-tight">
                        {{ alternative.name }}
                    </h2>
                    <p
                        class="mt-3 mb-6 line-clamp-3 text-sm text-muted-foreground"
                    >
                        {{ alternative.intro }}
                    </p>
                    <span
                        class="mt-auto inline-flex items-center gap-1 text-sm font-medium"
                    >
                        See the comparison
                        <IconArrowRight
                            class="size-4 transition-transform group-hover:translate-x-0.5"
                        />
                    </span>
                </Link>
            </div>
        </section>
    </SiteLayout>
</template>
