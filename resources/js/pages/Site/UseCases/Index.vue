<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { IconArrowRight } from '@tabler/icons-vue';

import JsonLd from '@/components/site/JsonLd.vue';
import PageHeader from '@/components/site/PageHeader.vue';
import Seo from '@/components/site/Seo.vue';
import SiteLayout from '@/layouts/site/SiteLayout.vue';
import site from '@/routes/site';

defineProps<{
    useCases: Array<{ slug: string; name: string; intro: string }>;
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
                eyebrow="Use cases"
                title="What people use Lua for"
                lead="Each one names the thing that goes wrong without a tool, and where Lua stops helping."
            />

            <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="useCase in useCases"
                    :key="useCase.slug"
                    :href="site.useCases.show.url(useCase.slug)"
                    :data-testid="`use-case-${useCase.slug}`"
                    class="group site-card flex flex-col p-6 transition-colors hover:border-foreground/20"
                >
                    <h2 class="text-xl font-semibold tracking-tight">
                        {{ useCase.name }}
                    </h2>
                    <p
                        class="mt-3 mb-6 line-clamp-4 text-sm leading-relaxed text-muted-foreground"
                    >
                        {{ useCase.intro }}
                    </p>
                    <span
                        class="mt-auto inline-flex items-center gap-1 text-sm font-medium"
                    >
                        Read on
                        <IconArrowRight
                            class="size-4 transition-transform group-hover:translate-x-0.5"
                        />
                    </span>
                </Link>
            </div>
        </section>
    </SiteLayout>
</template>
