<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { IconArrowRight } from '@tabler/icons-vue';

import JsonLd from '@/components/site/JsonLd.vue';
import PageHeader from '@/components/site/PageHeader.vue';
import Seo from '@/components/site/Seo.vue';
import SiteLayout from '@/layouts/site/SiteLayout.vue';

defineProps<{
    tools: Array<{
        slug: string;
        name: string;
        description: string;
        url: string;
    }>;
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
                eyebrow="Tools"
                title="Free tools, no account"
                lead="Three things adjacent to what Lua does. Two of them never send your URL anywhere, because they do not need to."
            />

            <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="tool in tools"
                    :key="tool.slug"
                    :href="tool.url"
                    :data-testid="`tool-${tool.slug}`"
                    class="group site-card flex flex-col p-6 transition-colors hover:border-foreground/20"
                >
                    <h2 class="text-xl font-semibold tracking-tight">
                        {{ tool.name }}
                    </h2>
                    <p
                        class="mt-3 mb-6 text-sm leading-relaxed text-muted-foreground"
                    >
                        {{ tool.description }}
                    </p>
                    <span
                        class="mt-auto inline-flex items-center gap-1 text-sm font-medium"
                    >
                        Open
                        <IconArrowRight
                            class="size-4 transition-transform group-hover:translate-x-0.5"
                        />
                    </span>
                </Link>
            </div>
        </section>
    </SiteLayout>
</template>
