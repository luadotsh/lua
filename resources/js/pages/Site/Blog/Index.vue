<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

import JsonLd from '@/components/site/JsonLd.vue';
import Seo from '@/components/site/Seo.vue';
import date from '@/date';
import SiteLayout from '@/layouts/site/SiteLayout.vue';
import site from '@/routes/site';

type Post = {
    slug: string;
    title: string;
    description: string;
    date: string | null;
    author: string;
    image: string | null;
    tags: string[];
    reading_time: number;
};

defineProps<{ posts: Post[]; seo: { title: string; description: string } }>();
</script>

<template>
    <Seo :title="seo.title" :description="seo.description" />
    <JsonLd
        :data="{
            '@type': 'Blog',
            name: 'Lua Blog',
            description: seo.description,
        }"
    />

    <SiteLayout>
        <section class="px-6 py-16 sm:px-10 sm:py-24">
            <p class="label">Blog</p>
            <h1
                class="mt-2 max-w-2xl font-display text-4xl font-semibold tracking-tight text-balance sm:text-5xl"
            >
                Notes from building a link shortener
            </h1>
            <p class="mt-4 max-w-2xl text-lg text-muted-foreground">
                What we learn about clicks, domains and analytics, written down
                so it is useful whether or not you use Lua.
            </p>

            <p
                v-if="posts.length === 0"
                class="mt-12 rounded-[0.875rem] border border-dashed border-border py-16 text-center text-muted-foreground"
            >
                Nothing published yet.
            </p>

            <div
                v-else
                class="mt-12 grid gap-x-8 gap-y-10 sm:grid-cols-2 lg:grid-cols-3"
            >
                <Link
                    v-for="post in posts"
                    :key="post.slug"
                    :href="site.blog.show.url(post.slug)"
                    :data-testid="`post-${post.slug}`"
                    class="group flex flex-col"
                >
                    <div
                        v-if="post.image"
                        class="mb-5 aspect-[16/9] overflow-hidden rounded-xl border border-border bg-muted"
                    >
                        <img
                            :src="post.image"
                            :alt="post.title"
                            class="size-full object-cover transition-transform duration-300 group-hover:scale-[1.02]"
                            loading="lazy"
                        />
                    </div>

                    <p class="text-xs text-muted-foreground">
                        <time v-if="post.date" :datetime="post.date">
                            {{ date.formatDate(post.date) }}
                        </time>
                        <span v-if="post.date"> · </span>
                        {{ post.reading_time }} min read
                    </p>

                    <h2
                        class="mt-2 text-xl font-semibold tracking-tight text-balance transition-colors group-hover:text-muted-foreground"
                    >
                        {{ post.title }}
                    </h2>
                    <p
                        class="mt-2 line-clamp-3 text-sm leading-relaxed text-muted-foreground"
                    >
                        {{ post.description }}
                    </p>
                </Link>
            </div>
        </section>
    </SiteLayout>
</template>
