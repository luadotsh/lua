<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { IconAlertTriangle, IconArrowLeft, IconCheck } from '@tabler/icons-vue';

import JsonLd from '@/components/site/JsonLd.vue';
import Seo from '@/components/site/Seo.vue';
import { Button } from '@/components/ui/button';
import SiteLayout from '@/layouts/site/SiteLayout.vue';
import { register } from '@/routes';
import site from '@/routes/site';

defineProps<{
    slug: string;
    useCase: {
        name: string;
        intro: string;
        problem: string;
        steps: Array<{ title: string; description: string }>;
        features: string[];
        caveat: string;
    };
    others: Array<{ slug: string; name: string }>;
    seo: { title: string; description: string };
}>();
</script>

<template>
    <Seo :title="seo.title" :description="seo.description" />
    <JsonLd
        :data="{
            '@type': 'Article',
            headline: seo.title,
            description: seo.description,
        }"
    />

    <SiteLayout>
        <article class="mx-auto max-w-4xl px-6 py-16 sm:px-10 sm:py-24">
            <Link
                :href="site.useCases.index.url()"
                data-testid="back-to-use-cases"
                class="inline-flex items-center gap-1.5 text-sm text-muted-foreground transition-colors hover:text-foreground"
            >
                <IconArrowLeft class="size-4" />
                All use cases
            </Link>

            <header class="mt-8">
                <p class="label">Use case</p>
                <h1
                    class="mt-2 font-display text-4xl font-semibold tracking-tight text-balance sm:text-5xl"
                >
                    {{ useCase.name }}
                </h1>
                <p class="mt-6 text-lg leading-relaxed text-muted-foreground">
                    {{ useCase.intro }}
                </p>
            </header>

            <section class="mt-14 border-l-2 border-primary/40 pl-6">
                <h2
                    class="text-sm font-medium tracking-wide text-muted-foreground uppercase"
                >
                    What goes wrong
                </h2>
                <p class="mt-3 leading-relaxed">{{ useCase.problem }}</p>
            </section>

            <section class="mt-16">
                <h2 class="font-display text-2xl font-semibold tracking-tight">
                    How it is done here
                </h2>
                <ol class="mt-8 space-y-8">
                    <li
                        v-for="(step, index) in useCase.steps"
                        :key="step.title"
                        class="grid gap-2 sm:grid-cols-[3rem_minmax(0,1fr)]"
                    >
                        <span
                            class="font-mono text-sm text-primary-text tabular-nums"
                        >
                            {{ String(index + 1).padStart(2, '0') }}
                        </span>
                        <div>
                            <h3 class="font-medium">{{ step.title }}</h3>
                            <p
                                class="mt-2 leading-relaxed text-muted-foreground"
                            >
                                {{ step.description }}
                            </p>
                        </div>
                    </li>
                </ol>
            </section>

            <section class="site-card mt-16 p-6 sm:p-8">
                <h2
                    class="text-sm font-medium tracking-wide text-muted-foreground uppercase"
                >
                    What carries it
                </h2>
                <ul class="mt-4 grid gap-3 sm:grid-cols-2">
                    <li
                        v-for="feature in useCase.features"
                        :key="feature"
                        class="flex items-center gap-2.5 text-sm"
                    >
                        <IconCheck class="size-4 shrink-0 text-primary-text" />
                        {{ feature }}
                    </li>
                </ul>
            </section>

            <!--
                Every page names where it stops helping. A use-case page that
                only sells is a page nobody believes the rest of.
            -->
            <section
                class="mt-8 flex gap-4 rounded-[0.875rem] border border-dashed border-border p-6 sm:p-8"
            >
                <IconAlertTriangle
                    class="mt-0.5 size-5 shrink-0 text-muted-foreground"
                />
                <div>
                    <h2 class="text-sm font-medium">Where this stops</h2>
                    <p class="mt-2 leading-relaxed text-muted-foreground">
                        {{ useCase.caveat }}
                    </p>
                </div>
            </section>

            <section class="site-card mt-16 px-6 py-12 text-center">
                <h2
                    class="font-display text-2xl font-semibold tracking-tight text-balance"
                >
                    Try it on your own links
                </h2>
                <p class="mx-auto mt-3 max-w-md text-muted-foreground">
                    Five links free, every analytics field, no card.
                </p>
                <Button class="mt-6" size="lg" as-child>
                    <Link :href="register.url()">Start for free</Link>
                </Button>
            </section>

            <nav v-if="others.length" class="mt-16 border-t border-border pt-8">
                <h2 class="label">Other use cases</h2>
                <ul class="mt-4 flex flex-wrap gap-x-6 gap-y-2">
                    <li v-for="other in others" :key="other.slug">
                        <Link
                            :href="site.useCases.show.url(other.slug)"
                            class="text-sm underline underline-offset-4"
                        >
                            {{ other.name }}
                        </Link>
                    </li>
                </ul>
            </nav>
        </article>
    </SiteLayout>
</template>
