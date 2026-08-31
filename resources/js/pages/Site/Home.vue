<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    IconArrowRight,
    IconChartBar,
    IconCheck,
    IconCode,
    IconLock,
    IconQrcode,
    IconServer,
    IconSparkles,
    IconTags,
    IconWorld,
    IconX,
} from '@tabler/icons-vue';
import AnalyticsMockup from '@/components/site/AnalyticsMockup.vue';
import ApiSample from '@/components/site/ApiSample.vue';
import FaqList from '@/components/site/FaqList.vue';
import JsonLd from '@/components/site/JsonLd.vue';
import Seo from '@/components/site/Seo.vue';
import ShortenMockup from '@/components/site/ShortenMockup.vue';
import SiteLayout from '@/layouts/site/SiteLayout.vue';
import { Button } from '@/components/ui/button';
import { register } from '@/routes';
import site from '@/routes/site';

defineProps<{
    alternatives: Array<{ slug: string; name: string }>;
    faq: Array<{ title: string; items: Array<{ question: string; answer: string }> }>;
    seo: { title: string; description: string };
}>();

/**
 * A real sequence, which is what earns the numbering. If these could be
 * reordered without loss they would be a list, not steps.
 */
/**
 * The title block. Every line is checkable, which is the whole reason it works
 * where a list of adjectives would not: a reader who doubts any of it can go
 * and look.
 *
 * It states what the product does, not how it is licensed. Open source is a
 * real differentiator and it is on the page in three other places, but leading
 * the hero with a licence answers a question almost nobody arrives with.
 */
const facts = [
    { label: 'Analytics', value: 'Full, every plan' },
    { label: 'History', value: 'Kept, any range' },
    { label: 'Your domain', value: 'Live in minutes' },
    { label: 'Interfaces', value: 'Web, REST, MCP' },
];

// The three things the product is, stated once so the rest of the page is
// elaboration rather than introduction.
const pillars = [
    {
        icon: IconWorld,
        title: 'Links on your domain',
        description:
            'A CNAME and a certificate. Every link wears your name, and keeps working if you ever change providers.',
        action: 'How it works',
        href: site.useCases.show.url('marketing-campaigns'),
    },
    {
        icon: IconChartBar,
        title: 'Analytics that do not expire',
        description:
            'Country, region, city, device, browser, referrer and UTM, over any range. On every plan, free included.',
        action: 'See what is recorded',
        href: site.blog.show.url('what-a-short-link-actually-records'),
    },
    {
        icon: IconCode,
        title: 'An API and an MCP server',
        description:
            'Every action the screens do, reachable over REST, and an assistant that can drive it directly.',
        action: 'Built for developers',
        href: site.useCases.show.url('developers'),
    },
];

const steps = [
    {
        title: 'Point a domain at Lua',
        description:
            'A CNAME, and a certificate that issues in minutes. A subdomain of a domain you already own works, so go.example.com costs nothing extra. Skip it and links work on the shared domain from the start.',
    },
    {
        title: 'Shorten and publish',
        description:
            'Paste the destination, pick the back-half, tag it. Every link gets a QR code, and can carry an expiry date, a password, or separate iOS and Android destinations.',
    },
    {
        title: 'Read what happened',
        description:
            'Every click becomes a row: country, region and city, device, browser and OS, referrer, and any UTM parameters. Filter by any of it, over any range.',
    },
];

// The two columns are the argument the whole site makes, stated plainly enough
// to disagree with.
const rented = [
    'The domain belongs to somebody else, and so does the trust it carries',
    'Analytics stop at whatever window your tier pays for',
    'A branded domain is the upgrade, so the link a reader recognises costs extra',
    'If the service closes, every link you printed closes with it',
    'Generic shortener domains get filtered as a category, not judged one by one',
];

const owned = [
    'The domain is yours, and the mapping is data you hold',
    'Every field, every date range, on every plan including free',
    'A domain is configuration, not a tier',
    'Change providers and every published link keeps resolving',
    'Your own domain carries your own reputation, which you can manage',
];

const features = [
    {
        icon: IconWorld,
        title: 'Your domain, not ours',
        description:
            'Point a domain at Lua and every link wears your name. It is configuration, not the tier you upgrade to.',
    },
    {
        icon: IconChartBar,
        title: 'Every click, kept',
        description:
            'Country, region, city, device, browser, OS, referrer and UTM — over any date range, not a window that quietly drops last year.',
    },
    {
        icon: IconQrcode,
        title: 'QR codes that count',
        description:
            'Every link has one, and scans are counted apart from clicks, so print and screen never share a number.',
    },
    {
        icon: IconLock,
        title: 'Expiry and passwords',
        description:
            'Retire a link on a date, or put a password in front of it. Send people somewhere else on iOS and Android.',
    },
    {
        icon: IconTags,
        title: 'Tags and workspaces',
        description:
            'Group links by campaign or client, and give a team its own workspace with its own domains.',
    },
    {
        icon: IconCode,
        title: 'An API that does everything',
        description:
            'The REST API reaches every action the screen does, because both call the same code. Nothing is dashboard-only.',
    },
    {
        icon: IconSparkles,
        title: 'Built for agents',
        description:
            'An MCP server ships with it, so an assistant can create a link, tag it and read its analytics with no scraping layer in between.',
    },
    {
        icon: IconServer,
        title: 'Run it yourself',
        description:
            'Open source and self-hostable. If the record of who clicked what should never leave your infrastructure, it does not have to.',
    },
];
</script>

<template>
    <Seo :title="seo.title" :description="seo.description" />
    <JsonLd
        :data="{
            '@type': 'SoftwareApplication',
            name: 'Lua',
            applicationCategory: 'BusinessApplication',
            description: seo.description,
            offers: { '@type': 'Offer', price: '0', priceCurrency: 'USD' },
        }"
    />

    <SiteLayout>
        <!--
            The globe is the argument: shortening a URL is the mechanism, and
            knowing where it was opened is the reason anyone pays for it. It is
            decoration, so it sits second in the source and is hidden from
            assistive tech.
        -->
        <!--
            The hero is the drawing sheet the rest of the page is ruled to.
            The headline sits in the column rather than floating centred in it,
            and the spec block takes the margin the way a title block does on a
            technical drawing. Everything in that block is true and checkable,
            which is the point: it is the differentiator, stated as fact rather
            than as a claim.
        -->
        <section class="site-rail-ticks relative border-b border-border">
            <div
                aria-hidden="true"
                class="site-grid pointer-events-none absolute inset-0 [mask-image:linear-gradient(to_bottom,#000,transparent_78%)] opacity-80"
            />

            <div class="relative px-6 pt-20 sm:px-10 sm:pt-28">
                <div class="lg:grid lg:grid-cols-[minmax(0,1fr)_14rem] lg:gap-12">
                    <div>
                        <h1
                            class="max-w-3xl font-display text-[2.75rem] leading-[0.98] font-semibold tracking-[-0.035em] text-balance sm:text-6xl lg:text-7xl"
                        >
                            Short links, and the story of every click
                        </h1>

                        <p class="mt-8 max-w-xl text-lg leading-relaxed text-muted-foreground sm:text-xl">
                            Put a link on your own domain, then see what
                            happened after it: country, device, referrer,
                            campaign. Kept for as long as the link exists.
                        </p>

                        <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                            <Button size="lg" as-child>
                                <Link :href="register.url()">Start for free</Link>
                            </Button>
                            <Button size="lg" variant="outline" as-child>
                                <Link :href="site.pricing.url()">See pricing</Link>
                            </Button>
                        </div>

                        <p class="mt-4 text-sm text-muted-foreground">
                            Five links free, no card. Analytics on every plan.
                        </p>
                    </div>

                    <!--
                        The title block. Facts, in the margin, in the label
                        register: the things that separate this from the
                        shorteners it competes with, none of them adjectives.
                    -->
                    <dl
                        class="mt-14 grid grid-cols-2 gap-y-6 border-t border-border pt-8 lg:mt-2 lg:grid-cols-1 lg:border-t-0 lg:border-l lg:pt-0 lg:pl-8"
                    >
                        <div v-for="fact in facts" :key="fact.label">
                            <dt class="label">{{ fact.label }}</dt>
                            <dd class="mt-1.5 font-mono text-sm">{{ fact.value }}</dd>
                        </div>
                    </dl>
                </div>

                <!--
                    Cropped by the section's own rule. The screen continuing
                    past the edge is what pulls the reader down instead of
                    leaving them at a hard stop.
                -->
                <div class="mt-14 max-h-[20.5rem] overflow-hidden sm:mt-16">
                    <AnalyticsMockup />
                </div>
            </div>
        </section>

        <section class="border-b border-border bg-muted/40">
            <div class="px-6 py-16 sm:px-10 sm:py-24">
                <div class="grid gap-4 sm:grid-cols-3">
                    <Link
                        v-for="pillar in pillars"
                        :key="pillar.title"
                        :href="pillar.href"
                        class="site-card group p-6 transition-shadow hover:shadow-md"
                    >
                        <component :is="pillar.icon" class="size-5 text-muted-foreground" />
                        <h2 class="mt-3 font-medium">{{ pillar.title }}</h2>
                        <p class="mt-2 text-sm leading-relaxed text-muted-foreground">
                            {{ pillar.description }}
                        </p>
                        <span
                            class="mt-4 inline-flex items-center gap-1 text-sm font-medium text-primary-text"
                        >
                            {{ pillar.action }}
                            <IconArrowRight
                                class="size-4 transition-transform group-hover:translate-x-0.5"
                            />
                        </span>
                    </Link>
                </div>
            </div>
        </section>

        <!--
            Three steps because it is genuinely a sequence: the domain has to
            exist before a link can wear it, and the link before the clicks.
            Numbering a set of unordered features would be decoration.
        -->
        <section class="border-b border-border">
            <div class="px-6 py-16 sm:px-10 sm:py-24">
                <div class="max-w-2xl">
                    <p class="label">How it works</p>
                    <h2 class="mt-2 font-display text-3xl font-semibold tracking-tight text-balance sm:text-4xl">
                        Three steps, and the third is the point
                    </h2>
                </div>

                <ol class="mt-14 grid gap-12 md:grid-cols-3 md:gap-0">
                    <li
                        v-for="(step, index) in steps"
                        :key="step.title"
                        class="md:px-8 md:first:pl-0 md:last:pr-0 md:not-first:border-l md:not-first:border-border"
                    >
                        <span
                            class="font-display text-5xl leading-none font-semibold text-border tabular-nums select-none"
                        >
                            {{ String(index + 1).padStart(2, '0') }}
                        </span>
                        <h3 class="mt-5 text-lg font-medium">{{ step.title }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-muted-foreground">
                            {{ step.description }}
                        </p>
                    </li>
                </ol>
            </div>
        </section>

        <section class="border-b border-border">
            <div
                class="grid items-center gap-12 px-6 py-16 sm:px-10 sm:py-24 lg:grid-cols-2"
            >
                <div>
                    <p class="label">Shorten</p>
                    <h2 class="mt-2 font-display text-3xl font-semibold tracking-tight text-balance sm:text-4xl">
                        A link people will actually click
                    </h2>
                    <p class="mt-4 text-lg text-muted-foreground">
                        A short link hides its destination, so the domain is the
                        only signal the reader gets. Yours says who is asking;
                        a stranger's says nothing, and gets filtered as a
                        category.
                    </p>
                    <Link
                        :href="site.alternatives.show.url('bitly')"
                        class="mt-6 inline-block text-sm font-medium underline underline-offset-4"
                    >
                        How that compares with Bitly
                    </Link>
                </div>

                <ShortenMockup />
            </div>
        </section>

        <section class="border-b border-border">
            <div class="px-6 py-16 sm:px-10 sm:py-24">
                <div class="max-w-2xl">
                    <p class="label">What you get</p>
                    <h2 class="mt-2 font-display text-3xl font-semibold tracking-tight text-balance sm:text-4xl">
                        The whole product, not a trial of it
                    </h2>
                </div>

                <div class="mt-12 grid gap-x-8 gap-y-10 sm:grid-cols-2 lg:grid-cols-4">
                    <div v-for="feature in features" :key="feature.title">
                        <component :is="feature.icon" class="size-5 text-muted-foreground" />
                        <h3 class="mt-3 font-medium">{{ feature.title }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-muted-foreground">
                            {{ feature.description }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="border-b border-border">
            <div
                class="grid gap-12 px-6 py-16 sm:px-10 sm:py-24 lg:grid-cols-[minmax(0,22rem)_minmax(0,1fr)] lg:items-center"
            >
                <div>
                    <p class="label">Build on it</p>
                    <h2 class="mt-2 font-display text-3xl font-semibold tracking-tight text-balance sm:text-4xl">
                        Nothing is dashboard-only
                    </h2>
                    <p class="mt-4 text-lg text-muted-foreground">
                        The REST API reaches every action the screens do,
                        because both call the same code underneath. An MCP
                        server ships with it, so an assistant can do the same
                        in words.
                    </p>
                    <Link
                        :href="site.useCases.show.url('developers')"
                        class="mt-6 inline-block text-sm font-medium underline underline-offset-4"
                    >
                        What developers get
                    </Link>
                </div>

                <ApiSample />
            </div>
        </section>

        <section class="border-b border-border">
            <div class="px-6 py-16 sm:px-10 sm:py-24">
                <div class="max-w-2xl">
                    <p class="label">The difference</p>
                    <h2 class="mt-2 font-display text-3xl font-semibold tracking-tight text-balance sm:text-4xl">
                        Rented links, or links you own
                    </h2>
                    <p class="mt-4 text-lg text-muted-foreground">
                        Most of what separates one shortener from another comes
                        down to who holds the domain.
                    </p>
                </div>

                <div class="mt-12 grid gap-4 md:grid-cols-2">
                    <div class="rounded-xl border border-border bg-muted/30 p-6 sm:p-8">
                        <h3 class="font-medium text-muted-foreground">On somebody else's domain</h3>
                        <ul class="mt-6 space-y-4">
                            <li
                                v-for="item in rented"
                                :key="item"
                                class="flex gap-3 text-sm leading-relaxed text-muted-foreground"
                            >
                                <IconX class="mt-0.5 size-4 shrink-0" />
                                <span>{{ item }}</span>
                            </li>
                        </ul>
                    </div>

                    <div class="rounded-xl border border-primary/30 bg-card p-6 sm:p-8">
                        <h3 class="font-medium">On yours, with Lua</h3>
                        <ul class="mt-6 space-y-4">
                            <li v-for="item in owned" :key="item" class="flex gap-3 text-sm leading-relaxed">
                                <IconCheck class="mt-0.5 size-4 shrink-0 text-primary-text" />
                                <span>{{ item }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="border-b border-border">
            <div
                class="gap-12 px-6 py-16 sm:px-10 sm:py-24 lg:grid lg:grid-cols-[20rem_minmax(0,1fr)]"
            >
                <div>
                    <p class="label">Questions</p>
                    <h2 class="mt-2 font-display text-3xl font-semibold tracking-tight text-balance sm:text-4xl">
                        The ones asked before signing up
                    </h2>
                    <Link
                        :href="site.faq.url()"
                        class="mt-6 inline-flex items-center gap-1.5 text-sm font-medium underline underline-offset-4"
                    >
                        Read all of them
                        <IconArrowRight class="size-4" />
                    </Link>
                </div>

                <div class="mt-10 min-w-0 lg:mt-0">
                    <FaqList
                        v-for="group in faq"
                        :key="group.title"
                        :items="group.items"
                        class="-mt-px"
                    />
                </div>
            </div>
        </section>

        <section class="border-b border-border">
            <div class="px-6 py-12 sm:px-10">
                <div class="flex flex-wrap items-center gap-x-6 gap-y-3 text-sm">
                    <span class="text-muted-foreground">Comparing options?</span>
                    <Link
                        v-for="alternative in alternatives"
                        :key="alternative.slug"
                        :href="site.alternatives.show.url(alternative.slug)"
                        class="underline underline-offset-4 transition-colors hover:text-primary-text"
                    >
                        Lua vs {{ alternative.name }}
                    </Link>
                </div>
            </div>
        </section>

        <section>
            <div class="px-6 py-16 sm:px-10 sm:py-24">
                <div class="site-card px-6 py-16 text-center">
                    <h2 class="font-display text-3xl font-semibold tracking-tight text-balance sm:text-4xl">
                        Make your first link in a minute
                    </h2>
                    <p class="mx-auto mt-4 max-w-md text-muted-foreground">
                        Sign up, paste a URL, and watch the clicks arrive. Bring
                        a domain whenever you are ready.
                    </p>
                    <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                        <Button size="lg" as-child>
                            <Link :href="register.url()">Start for free</Link>
                        </Button>
                        <Button size="lg" variant="outline" as-child>
                            <Link :href="site.alternatives.index.url()">
                                Compare with what you use
                            </Link>
                        </Button>
                    </div>
                </div>
            </div>
        </section>
    </SiteLayout>
</template>
