<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { IconArrowLeft } from '@tabler/icons-vue';

import BlogToc from '@/components/site/BlogToc.vue';
import JsonLd from '@/components/site/JsonLd.vue';
import Seo from '@/components/site/Seo.vue';
import date from '@/date';
import SiteLayout from '@/layouts/site/SiteLayout.vue';
import site from '@/routes/site';

/**
 * The body arrives as HTML: the markdown is parsed on the server, so the
 * article is in the source a crawler reads and there is no parser shipped to
 * the browser to render text it could have been handed.
 */
defineProps<{
    post: {
        slug: string;
        title: string;
        description: string;
        date: string | null;
        author: string;
        image: string | null;
        tags: string[];
        reading_time: number;
        html: string;
        headings: Array<{ id: string; text: string; level: number }>;
    };
    seo: { title: string; description: string; image: string | null };
}>();
</script>

<template>
    <Seo
        :title="seo.title"
        :description="seo.description"
        :image="seo.image ?? undefined"
    />
    <JsonLd
        :data="{
            '@type': 'BlogPosting',
            headline: post.title,
            description: post.description,
            datePublished: post.date,
            author: { '@type': 'Organization', name: post.author },
        }"
    />

    <SiteLayout>
        <div class="px-6 py-16 sm:px-10 sm:py-24">
            <Link
                :href="site.blog.index.url()"
                data-testid="back-to-blog"
                class="inline-flex items-center gap-1.5 text-sm text-muted-foreground transition-colors hover:text-foreground"
            >
                <IconArrowLeft class="size-4" />
                All posts
            </Link>

            <header class="mt-8 max-w-3xl">
                <p class="text-sm text-muted-foreground">
                    <time v-if="post.date" :datetime="post.date">
                        {{ date.formatDate(post.date) }}
                    </time>
                    <span v-if="post.date"> · </span>
                    {{ post.reading_time }} min read
                </p>
                <h1
                    class="mt-3 font-display text-4xl font-semibold tracking-tight text-balance sm:text-5xl"
                >
                    {{ post.title }}
                </h1>
                <p class="mt-4 text-lg text-balance text-muted-foreground">
                    {{ post.description }}
                </p>
            </header>

            <img
                v-if="post.image"
                :src="post.image"
                :alt="post.title"
                class="mt-10 aspect-[16/9] w-full rounded-xl border border-border object-cover"
            />

            <div
                class="mt-12 lg:grid lg:grid-cols-[minmax(0,1fr)_15rem] lg:gap-14"
            >
                <!--
                    `prose` comes from @tailwindcss/typography, which is what
                    styles markup the server generated and this file never sees.
                -->
                <article
                    data-testid="blog-body"
                    class="prose max-w-3xl min-w-0 prose-neutral prose-headings:scroll-mt-24 prose-headings:tracking-tight prose-a:underline-offset-4 prose-code:rounded prose-code:bg-muted prose-code:px-1 prose-code:py-0.5 prose-code:font-normal prose-code:before:content-none prose-code:after:content-none"
                    v-html="post.html"
                />

                <!--
                    Second in the source so a screen reader and a phone reach
                    the article first; `order` puts it back on the right on a
                    wide screen, where it is sticky beside the text.
                -->
                <aside class="hidden lg:block">
                    <div class="sticky top-24">
                        <BlogToc :headings="post.headings" />
                    </div>
                </aside>
            </div>
        </div>
    </SiteLayout>
</template>
