<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

/**
 * One place that emits title, description, canonical and both card formats,
 * so a page never ships with an OG tag but no Twitter one — the usual way a
 * link preview ends up half empty.
 */
const props = withDefaults(
    defineProps<{
        /** Without the brand: createInertiaApp's title callback appends it. */
        title: string;
        description: string;
        /** Defaults to the URL being viewed, which is what we want indexed. */
        canonical?: string;
        image?: string;
    }>(),
    { canonical: undefined, image: undefined },
);

// OG and Twitter titles bypass that callback, so they carry the brand
// themselves — otherwise a shared link previews as a bare "Pricing".
const socialTitle = computed(() => `${props.title} — Lua`);

const page = usePage();

const origin = computed(() => {
    const url = page.props.appUrl as string | undefined;

    return (url ?? '').replace(/\/$/, '');
});

const canonicalUrl = computed(
    () => props.canonical ?? `${origin.value}${page.url.split('?')[0]}`,
);

const imageUrl = computed(
    () => `${origin.value}${props.image ?? '/images/lua/full-color.svg'}`,
);
</script>

<template>
    <Head>
        <title>{{ title }}</title>
        <meta name="description" :content="description" />
        <link rel="canonical" :href="canonicalUrl" />

        <meta property="og:type" content="website" />
        <meta property="og:site_name" content="Lua" />
        <meta property="og:url" :content="canonicalUrl" />
        <meta property="og:title" :content="socialTitle" />
        <meta property="og:description" :content="description" />
        <meta property="og:image" :content="imageUrl" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="socialTitle" />
        <meta name="twitter:description" :content="description" />
        <meta name="twitter:image" :content="imageUrl" />
    </Head>
</template>
