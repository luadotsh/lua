<script setup lang="ts">
import LegalDocument, { type LegalSection } from '@/components/site/LegalDocument.vue';

defineProps<{ seo: { title: string; description: string } }>();

// Written from what the code actually records — see ProcessLinkStat and the
// link_stats table. If either changes, this page changes with it.
const sections: LegalSection[] = [
    {
        id: 'summary',
        title: 'The short version',
        paragraphs: [
            'Lua is a link shortener, so measuring clicks is the product rather than a side effect. We are direct about that: this page says what is recorded when someone clicks one of your links, what we do with it, and what we never do.',
            'We do not sell data, we do not run advertising, and we do not build profiles that follow people between our customers.',
        ],
    },
    {
        id: 'account-data',
        title: 'What we collect about you',
        bullets: [
            'Your name, email address and password hash',
            'Your workspace, its members and the links, tags and domains you create',
            'Billing details, held by our payment processor rather than by us — we store the identifiers needed to match a subscription to a workspace',
            'Operational logs of requests to the app, kept briefly for debugging and abuse handling',
        ],
    },
    {
        id: 'click-data',
        title: 'What we record when a link is clicked',
        paragraphs: [
            'Every click on one of your links creates an event, so that you can see how the link performed. The event holds:',
        ],
        bullets: [
            'The link, the time, and whether the click came from a QR code',
            'An approximate location — country, region and city — derived from the IP address',
            'The IP address itself',
            'Browser, operating system, device type and preferred language, read from the request headers',
            'The referring page, and any UTM parameters on the URL',
        ],
    },
    {
        id: 'ip-addresses',
        title: 'About IP addresses',
        paragraphs: [
            'An IP address can identify a person, so it deserves naming separately rather than hiding in a list. We store it against the click event because it is what location is derived from and what makes abuse and click fraud investigable.',
            'It is never shown in the analytics screens, never shared with anyone, and it is deleted when the event is. If you self-host Lua, the IP never leaves your own infrastructure at all.',
        ],
    },
    {
        id: 'cookies',
        title: 'Cookies',
        paragraphs: [
            'The app sets a session cookie so you stay signed in, and remembers your theme choice. That is all it needs.',
            'Following a short link sets no cookie. There is nothing to consent to at the point of the redirect, and we do not track a person from one of your links to another.',
        ],
    },
    {
        id: 'why',
        title: 'Why we hold any of it',
        bullets: [
            'To run the service you signed up for — resolving links and reporting on them',
            'To bill you, where you are on a paid plan',
            'To keep the service working and to investigate abuse of it',
            'To reply when you contact us',
        ],
    },
    {
        id: 'sharing',
        title: 'Who else sees it',
        paragraphs: [
            'A small number of processors, each doing one job and none of them permitted to use your data for their own purposes: our hosting and database provider, our payment processor, our email delivery provider, and our error-monitoring service.',
            'We will disclose data where the law requires it. We do not sell it, and we do not share it for advertising.',
        ],
    },
    {
        id: 'retention',
        title: 'How long we keep it',
        paragraphs: [
            'Click events are kept while the link and the workspace exist, so that historic campaigns remain readable. Deleting a link deletes its events.',
            'Closing your account deletes the workspace and everything in it after a short grace period, except records we must keep for accounting.',
        ],
    },
    {
        id: 'rights',
        title: 'Your rights',
        paragraphs: [
            'Depending on where you live you may have the right to access, correct, export or delete your personal data, and to object to some processing.',
            'Most of this you can do yourself: the app exports over its API and deletes what you ask it to. For anything else, write to us and we will act on it.',
        ],
    },
    {
        id: 'security',
        title: 'Security',
        paragraphs: [
            'Traffic is encrypted in transit, passwords are hashed, and API tokens are stored hashed and shown once. Access to production data is limited to the people who need it to operate the service.',
        ],
    },
    {
        id: 'changes',
        title: 'Changes to this policy',
        paragraphs: [
            'If what we collect changes, this page changes with it, and the date at the top shows the current version. Material changes are announced before they take effect.',
        ],
    },
    {
        id: 'contact',
        title: 'Contact',
        paragraphs: [
            'Privacy questions, or a request about your data: hello@lua.sh.',
        ],
    },
];
</script>

<template>
    <LegalDocument
        title="Privacy Policy"
        last-updated="August 31, 2026"
        lead="What Lua records when someone clicks one of your links, why it records it, and how long any of it stays around."
        :sections="sections"
        :seo="seo"
    />
</template>
