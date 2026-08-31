<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { IconArrowLeft, IconCheck, IconX } from '@tabler/icons-vue';
import JsonLd from '@/components/site/JsonLd.vue';
import Seo from '@/components/site/Seo.vue';
import SiteLayout from '@/layouts/site/SiteLayout.vue';
import { Button } from '@/components/ui/button';
import { register } from '@/routes';
import site from '@/routes/site';

/**
 * Renders whatever `config/alternatives.php` holds for one competitor. Adding
 * a rival is a new key in that file — this template never changes.
 */
type Row = { feature: string; lua: string; competitor: string };
type PriceRow = { tier: string; lua: string; competitor: string };
type FitList = { title: string; items: string[] };

defineProps<{
    slug: string;
    alternative: {
        name: string;
        intro: string;
        reasons: Array<{ title: string; description: string }>;
        comparison: Row[];
        pricing: PriceRow[];
        fit: { good: FitList; bad: FitList };
    };
    seo: { title: string; description: string };
}>();
</script>

<template>
    <Seo :title="seo.title" :description="seo.description" />
    <JsonLd
        :data="{
            '@type': 'Article',
            headline: `Lua vs ${alternative.name}`,
            description: seo.description,
            about: { '@type': 'SoftwareApplication', name: alternative.name },
        }"
    />

    <SiteLayout>
        <article class="mx-auto max-w-4xl px-6 py-16 sm:px-10 sm:py-24">
            <Link
                :href="site.alternatives.index.url()"
                data-testid="back-to-alternatives"
                class="inline-flex items-center gap-1.5 text-sm text-muted-foreground transition-colors hover:text-foreground"
            >
                <IconArrowLeft class="size-4" />
                All comparisons
            </Link>

            <header class="mt-8">
                <p class="label">Lua vs</p>
                <h1 class="mt-1 font-display text-4xl font-semibold tracking-tight text-balance sm:text-5xl">
                    {{ alternative.name }}
                </h1>
                <p class="mt-6 text-lg leading-relaxed text-muted-foreground">
                    {{ alternative.intro }}
                </p>
            </header>

            <section class="mt-16">
                <h2 class="text-2xl font-semibold tracking-tight">Why people move</h2>
                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div
                        v-for="reason in alternative.reasons"
                        :key="reason.title"
                        class="site-card p-5"
                    >
                        <h3 class="font-medium">{{ reason.title }}</h3>
                        <p class="mt-2 text-sm text-muted-foreground">
                            {{ reason.description }}
                        </p>
                    </div>
                </div>
            </section>

            <section class="mt-16">
                <h2 class="text-2xl font-semibold tracking-tight">
                    Feature by feature
                </h2>
                <!-- Its own scroll container: a wide table must never make the
                     page itself scroll sideways on a phone. -->
                <div class="mt-6 overflow-x-auto rounded-xl border border-border">
                    <table class="w-full min-w-[36rem] text-left text-sm">
                        <thead class="border-b border-border bg-muted/40">
                            <tr>
                                <th scope="col" class="px-4 py-3 font-medium">Feature</th>
                                <th scope="col" class="px-4 py-3 font-medium">Lua</th>
                                <th scope="col" class="px-4 py-3 font-medium">
                                    {{ alternative.name }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="row in alternative.comparison"
                                :key="row.feature"
                                class="border-b border-border last:border-0"
                            >
                                <th scope="row" class="px-4 py-3 font-normal text-muted-foreground">
                                    {{ row.feature }}
                                </th>
                                <td class="px-4 py-3">{{ row.lua }}</td>
                                <td class="px-4 py-3 text-muted-foreground">
                                    {{ row.competitor }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="mt-16">
                <h2 class="text-2xl font-semibold tracking-tight">What it costs</h2>
                <div class="mt-6 overflow-x-auto rounded-xl border border-border">
                    <table class="w-full min-w-[36rem] text-left text-sm">
                        <thead class="border-b border-border bg-muted/40">
                            <tr>
                                <th scope="col" class="px-4 py-3 font-medium">Where you are</th>
                                <th scope="col" class="px-4 py-3 font-medium">Lua</th>
                                <th scope="col" class="px-4 py-3 font-medium">
                                    {{ alternative.name }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="row in alternative.pricing"
                                :key="row.tier"
                                class="border-b border-border last:border-0"
                            >
                                <th scope="row" class="px-4 py-3 font-normal text-muted-foreground">
                                    {{ row.tier }}
                                </th>
                                <td class="px-4 py-3">{{ row.lua }}</td>
                                <td class="px-4 py-3 text-muted-foreground">
                                    {{ row.competitor }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!--
                Both halves, side by side and the same size. A comparison page
                that only lists reasons to switch is an advert; naming who
                should stay is what makes the rest of it worth believing.
            -->
            <section class="mt-16 grid gap-4 sm:grid-cols-2">
                <div class="site-card p-6">
                    <h2 class="font-medium">{{ alternative.fit.good.title }}</h2>
                    <ul class="mt-4 space-y-3">
                        <li
                            v-for="item in alternative.fit.good.items"
                            :key="item"
                            class="flex gap-3 text-sm"
                        >
                            <IconCheck class="mt-0.5 size-4 shrink-0 text-emerald-600" />
                            <span>{{ item }}</span>
                        </li>
                    </ul>
                </div>

                <div class="site-card p-6">
                    <h2 class="font-medium">{{ alternative.fit.bad.title }}</h2>
                    <ul class="mt-4 space-y-3">
                        <li
                            v-for="item in alternative.fit.bad.items"
                            :key="item"
                            class="flex gap-3 text-sm text-muted-foreground"
                        >
                            <IconX class="mt-0.5 size-4 shrink-0" />
                            <span>{{ item }}</span>
                        </li>
                    </ul>
                </div>
            </section>

            <section class="mt-16 site-card px-6 py-12 text-center">
                <h2 class="text-2xl font-semibold tracking-tight text-balance">
                    Try it against your own links
                </h2>
                <p class="mx-auto mt-3 max-w-md text-muted-foreground">
                    Free to start, no card. Bring a domain when you are ready.
                </p>
                <Button class="mt-6" size="lg" as-child>
                    <Link :href="register.url()">Start for free</Link>
                </Button>
            </section>
        </article>
    </SiteLayout>
</template>
