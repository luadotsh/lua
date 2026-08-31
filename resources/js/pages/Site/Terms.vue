<script setup lang="ts">
import LegalDocument, {
    type LegalSection,
} from '@/components/site/LegalDocument.vue';

defineProps<{ seo: { title: string; description: string } }>();

const sections: LegalSection[] = [
    {
        id: 'agreement',
        title: 'The agreement',
        paragraphs: [
            'These terms cover your use of the hosted Lua service. Creating an account means you accept them. If you are agreeing on behalf of a company, you are confirming you may bind it.',
            'Lua is also open source. Running your own copy is governed by the licence in the repository, not by this document.',
        ],
    },
    {
        id: 'account',
        title: 'Your account',
        paragraphs: [
            'You are responsible for what happens under your account, including keeping your credentials and API tokens to yourself. Tell us promptly if you believe either has been exposed.',
            'You must be old enough to enter a contract where you live.',
        ],
    },
    {
        id: 'acceptable-use',
        title: 'What you may not use it for',
        paragraphs: [
            'A short link hides its destination, which makes it attractive to people who would rather you did not see where you are going. That is what this section exists to stop.',
        ],
        bullets: [
            'Phishing, malware, or anything designed to deceive the person clicking',
            'Content that is illegal where you or your audience are',
            'Disguising a destination in order to evade a filter, a moderator, or a platform rule',
            'Sending traffic you did not obtain consent for, including unsolicited bulk messages',
            'Automated load beyond your plan, or attempts to reach data belonging to another workspace',
        ],
    },
    {
        id: 'links',
        title: 'Links and their destinations',
        paragraphs: [
            'You own the destinations you point at and are responsible for them. We do not review links before they resolve, but we may disable one, or an account, when we have reason to believe this section is being broken.',
            'A short link only works while the account behind it does. Closing an account, or letting a custom domain lapse, stops its links resolving — plan for that before printing one on something permanent.',
        ],
    },
    {
        id: 'plans',
        title: 'Plans, billing and cancellation',
        paragraphs: [
            'Paid plans bill in advance for the period you choose and renew automatically until cancelled. Cancelling stops the next renewal; the plan stays active until the end of the period you have paid for.',
            'Plan limits apply as published on the pricing page. Going over one stops new work of that kind rather than deleting anything you already have.',
            'Prices can change. If they do for a plan you are on, we will tell you before the change takes effect on your next renewal.',
        ],
    },
    {
        id: 'data',
        title: 'Your data',
        paragraphs: [
            'Your links, your click events and everything else you put into Lua remain yours. You can export them over the API at any time, and you can delete them.',
            'We use them to run the service for you — nothing else. What we collect and how long we keep it is described in the privacy policy.',
        ],
    },
    {
        id: 'availability',
        title: 'Availability',
        paragraphs: [
            'We work to keep redirects fast and available, but the hosted service is provided as it is, without a guarantee of uninterrupted operation. Planned maintenance is announced in advance where we can.',
        ],
    },
    {
        id: 'termination',
        title: 'Ending it',
        paragraphs: [
            'You can close your account whenever you like. We may suspend or close an account that breaks the acceptable-use section, or that goes unpaid.',
            'On closure, links stop resolving and the data is deleted after a short grace period. Export anything you want to keep before you close.',
        ],
    },
    {
        id: 'liability',
        title: 'Liability',
        paragraphs: [
            'To the extent the law allows, our liability arising out of the service is limited to what you paid us in the twelve months before the claim, and we are not liable for indirect or consequential loss.',
            'Nothing here limits liability that cannot lawfully be limited.',
        ],
    },
    {
        id: 'changes',
        title: 'Changes to these terms',
        paragraphs: [
            'We may update these terms. Material changes will be announced before they take effect, and the date at the top of this page always shows the current version. Continuing to use Lua after that means you accept the new version.',
        ],
    },
    {
        id: 'contact',
        title: 'Contact',
        paragraphs: ['Questions about these terms: hello@lua.sh.'],
    },
];
</script>

<template>
    <LegalDocument
        title="Terms of Service"
        last-updated="August 31, 2026"
        lead="Plain terms for a small product: what you can do with Lua, what we will not host, and what happens when either of us wants to stop."
        :sections="sections"
        :seo="seo"
    />
</template>
