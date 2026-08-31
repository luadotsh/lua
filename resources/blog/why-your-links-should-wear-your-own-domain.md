---
title: Why your short links should wear your own domain
description: A branded short domain is not vanity. It changes whether people click, keeps you out of spam filters, and decides if your links outlive the shortener.
date: 2026-08-27
author: Lua
image: /images/blog/why-your-links-should-wear-your-own-domain/cover.jpg
image_credit: Photo by T on Unsplash (https://unsplash.com/photos/pj-fogg-co-apothecary-sign-at-night-AT_P72h3UEg)
tags: [domains, deliverability, branding]
---

Most link shorteners will happily hand you `theirbrand.co/aB3x9`. It works, it is short, and it costs nothing. It is also the one piece of your marketing where you have put somebody else's name in front of your audience, and unlike most such trades this one has a straightforward way out: point a domain you already own at the shortener and every link you make from then on carries your name instead.

Here is why that is worth ten minutes of DNS.

## People decide whether to click before they click

A short link hides its destination. That is the mechanism, and it is also the problem. The reader is being asked to follow a URL that tells them nothing about where it goes, so the only signal they get is the domain.

`example.com/spring` and `xyz.co/aB3x9` make different promises. One names a party the reader may already know. The other names a service they have no relationship with, in exactly the URL shape that phishing uses, because phishing uses generic shorteners for the same reason marketers do.

Published lift figures for branded domains vary far too much between studies to quote one at you honestly. Some are vendor-run, most measure different audiences, and a few are old enough to predate the current wave of link caution. The direction they agree on is not mysterious, and the reasoning holds without a number attached: a link people recognise gets clicked more often than a link people cannot place.

This matters most in exactly the places short links are most useful. A printed QR code, an SMS, a bio field, a slide at the back of a conference room. Places where there is no surrounding page to establish who is asking.

![A cafe logo on a glass door](/images/blog/why-your-links-should-wear-your-own-domain/inline-1.jpg)

## Generic shorteners get filtered as a category

Because generic shorteners are heavily used for phishing, security systems treat them as a class rather than judging each link on its own:

- Corporate mail gateways rewrite or strip them.
- Some networks block the best known ones outright.
- Spam filters weigh them, particularly on mail to people who have not written to you before.
- Chat platforms show an interstitial warning before following one.

None of that is a judgement about your specific link. It is a judgement about the domain your link is wearing, and there is nobody to appeal to. You inherit the reputation of every other person using that shortener, including the ones sending malware this morning.

A domain you control carries your own reputation instead. That is a smaller reputation to start with, and it is one you can actually manage: you decide what gets published on it, and a domain with a history of sending people to real pages accumulates trust rather than borrowing someone else's suspicion.

## A link on your own domain outlives the shortener

This is the argument that only becomes visible when it goes wrong, which is why it rarely makes it into a feature comparison.

Short links have a habit of ending up somewhere permanent. A printed flyer. A conference badge. A product label. A book. Packaging for a product with a two year shelf life. If those links sit on a shortener's domain, they live exactly as long as that company's willingness to keep serving them, and that willingness has run out before. Shorteners have shut down and taken every link with them, and the people who had printed those links had no recourse, because they never owned the part that mattered.

![Posters layered on a brick wall](/images/blog/why-your-links-should-wear-your-own-domain/inline-2.jpg)

A link on your own domain is different in kind, not in degree. The mapping from `example.com/spring` to its destination is data you hold. The domain is a name you renew. Change providers and you point the DNS somewhere else, and every link you ever published keeps resolving. Self-host it and you were never dependent on anybody to begin with. The flyer does not care which server answers.

That is the actual case for a branded domain, and it has nothing to do with looking professional. It is the difference between owning your links and renting them.

## What it takes to set one up

Less than people expect. The shape is the same on every provider worth using:

1. **Pick a domain.** It does not need to be short and clever. A subdomain of the domain you already own works perfectly and costs nothing extra, so `go.example.com` or `link.example.com` is usually the right answer. Recognition beats brevity, and a subdomain inherits the recognition you already have.
2. **Point a CNAME at your shortener.**
3. **Wait for the certificate.** Usually minutes.

That is the whole process. Where providers differ is what it costs you. On most shorteners a custom domain is the paid tier, which is a strange thing to put behind a paywall given it is the reader's only defence against a link that could be anything.

## One thing to decide before you start

Pick the domain you are willing to keep. Moving between shorteners is easy, because the links move with the domain. Moving between domains is not, because every link already published stops working.

Two consequences follow from that. Do not use a campaign-specific domain for links you intend to print, since the campaign will end before the flyer does. And do not use a domain you might let expire, because an expired short domain does not fail politely: it gets bought by somebody who noticed the traffic, and then your printed links point at their page.

Treat the domain as infrastructure rather than as a campaign asset, and the rest of it is a CNAME record.

## What to do about links you have already published

Switching does not retroactively fix the links already out in the world, and pretending otherwise is how people end up breaking things. The old ones keep pointing at the old shortener for as long as it keeps answering, so treat the change as a line drawn on a date rather than a migration.

Three things worth doing in order. Make every new link on the new domain starting today, because the pile only grows. Update the places you control, so your site, your email footers, your bio fields and your scheduled posts. Leave the printed ones alone, because you cannot recall a flyer and the old links will keep working until the shortener stops.

If the old shortener lets you export, take the export anyway. A list of which short link pointed where is the record you will want when somebody asks in a year why a QR code on a poster goes to a dead page, and it is the one thing you cannot reconstruct after the account is closed.

## Where Lua lands on this

On [Lua](/) a custom domain is configuration rather than a tier, on the hosted plans and on a self-hosted install alike. That is a deliberate choice, and the reasoning is the same argument this post makes: a short link people hesitate to click is not a working product, so charging extra for the thing that makes them click is charging for the product to work.

You can [see the plans](/pricing), or read how that compares with [Bitly](/alternatives/bitly), which puts the domain behind the upgrade.
