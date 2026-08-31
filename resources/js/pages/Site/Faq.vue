<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import FaqList from '@/components/site/FaqList.vue';
import JsonLd from '@/components/site/JsonLd.vue';
import Seo from '@/components/site/Seo.vue';
import SiteLayout from '@/layouts/site/SiteLayout.vue';
import { Button } from '@/components/ui/button';
import { register } from '@/routes';

type Group = { title: string; items: Array<{ question: string; answer: string }> };

const props = defineProps<{ groups: Group[]; seo: { title: string; description: string } }>();

// FAQPage structured data covering every question, so the answers are eligible
// as rich results rather than only readable on the page.
const schema = computed(() => ({
    '@type': 'FAQPage',
    mainEntity: props.groups.flatMap((group) =>
        group.items.map((item) => ({
            '@type': 'Question',
            name: item.question,
            acceptedAnswer: { '@type': 'Answer', text: item.answer },
        })),
    ),
}));
</script>

<template>
    <Seo :title="seo.title" :description="seo.description" />
    <JsonLd :data="schema" />

    <SiteLayout>
        <section class="px-6 py-16 sm:px-10 sm:py-24">
            <div class="max-w-2xl">
                <p class="label">FAQ</p>
                <h1 class="mt-2 font-display text-4xl font-semibold tracking-tight text-balance sm:text-5xl">
                    Questions people actually ask
                </h1>
                <p class="mt-4 text-lg text-muted-foreground">
                    Including the ones with awkward answers.
                </p>
            </div>

            <!--
                Two columns on wide screens: the group titles sit in a sticky
                rail so the reader keeps their place in a long page.
            -->
            <div class="mt-14 space-y-16">
                <section
                    v-for="group in groups"
                    :key="group.title"
                    :id="group.title.toLowerCase().replaceAll(' ', '-')"
                    class="lg:grid lg:grid-cols-[14rem_minmax(0,1fr)] lg:gap-14"
                >
                    <h2 class="mb-6 text-sm font-medium tracking-wide text-muted-foreground uppercase lg:mb-0">
                        {{ group.title }}
                    </h2>
                    <FaqList :items="group.items" class="min-w-0" />
                </section>
            </div>

            <div class="mt-20 site-card px-6 py-12 text-center">
                <h2 class="font-display text-2xl font-semibold tracking-tight text-balance">
                    Still unsure?
                </h2>
                <p class="mx-auto mt-3 max-w-md text-muted-foreground">
                    The free plan answers most of it faster than we can. Five
                    links, every analytics field, no card.
                </p>
                <Button class="mt-6" size="lg" as-child>
                    <Link :href="register.url()">Start for free</Link>
                </Button>
            </div>
        </section>
    </SiteLayout>
</template>
