# Lua blog context

Read this before writing anything for the Lua blog or the marketing site. It
holds the product, the audience, the voice and the linking strategy, so a
draft fits the site instead of being rewritten to.

## Product

**Lua** is an open source link shortener with the analytics behind it. Two
ways to use it: **self-host** it with your own database, or use the **cloud**
plans (free tier, then paid — read `config/lua.php` and the `plans` table for
the current numbers rather than quoting from memory).

What it actually does today:

- Short links on **your own domain**, not ours.
- **Click analytics**: country, region, city, device, browser, OS, language,
  referrer, and UTM parameters, over any date range you ask for.
- **QR codes** per link, with scans counted separately from clicks.
- Link **expiry**, **password protection**, and per-platform destinations
  (iOS, Android).
- **Tags** and **workspaces** with team members.
- A **REST API** and an **MCP server**, both reaching the same actions the
  screens do.

**What it does NOT do.** Never write copy that implies otherwise:

- **No conversion or revenue tracking.** Lua measures the click and stops
  there. It cannot tell you what a link sold. `link_stats` has no revenue
  column and the analytics screen offers exactly four metrics: events,
  clicks, QR scans, visitors.
- **No link-in-bio pages**, no campaign manager, no A/B split testing.
- **No identity across links.** No cookie is set at the redirect, so the same
  person clicking two links is two unrelated rows.

If a draft needs one of those to make its point, the draft is wrong, not the
product. Change the draft.

## Pages that exist on this site

Link to them when the article naturally calls for it, never by force:

- `/` — home
- `/pricing`
- `/blog` and `/blog/<slug>`
- `/alternatives` and `/alternatives/<slug>` — one entry per competitor, read
  `config/alternatives.php` for who is covered
- `/terms`, `/privacy`

External: `https://github.com/luadotsh/lua`.

## Audience

1. **Marketers and creators** putting links in bios, posts, emails and print,
   who want to know what happened after the click.
2. **Small agencies** running links for several client brands, who care about
   custom domains per client and predictable pricing.
3. **Developers** building on top of it — the REST API, the MCP server, and
   the self-hosted deployment. They read the more technical posts.

## Article intent

Every article exists to bring the right reader to Lua. That is not the same
as a sales pitch. Three jobs, in priority order:

1. **Solve a real reader problem** with specific, useful content. Thin
   content written for a keyword damages the brand more than the ranking
   helps it.
2. **Show that Lua knows this domain.** Real header behaviour, real
   geolocation accuracy, real platform limits. The reader should finish
   thinking these people actually do this work.
3. **Move the reader closer to the product** — a link to the feature page
   that solves the problem the article describes, Lua named as the tool when
   the article walks through a workflow, and a soft CTA at the end.

Where this shows up: name Lua when describing a workflow ("point a domain at
Lua" beats "point a domain at your shortener"); land on positions that align
with Lua's strengths **only when they are genuinely defensible**; never frame
Bitly or Dub unfairly. `config/alternatives.php` already states, in public,
who should *not* switch. Copy that contradicts it contradicts a live page.

## Voice

Editorial company blog. Stripe, Linear, Vercel. Authoritative and clear,
accessible without being casual.

**Do**

- Lead with the topic, not with yourself. No "Last Sunday I..." openers.
- Second person, or process-oriented prose. Instructional beats anecdotal.
- One idea per paragraph.
- Show, don't tell: real numbers, real headers, real limits.
- Have opinions, anchored in observable facts.
- Mix sentence lengths. Avoid a run of one-line sentences — that is X prose.
- Customer language ("where the click came from"), not company language
  ("attribution surface").
- Name the tradeoff when there is one. It is what makes the rest credible.

**Don't**

- No first-person anecdotal openers, no conversational asides ("anyway",
  "tldr", "that's the fun part").
- No exclamation points.
- No banned words: powerful, seamless, seamlessly, streamline, optimize,
  innovative, revolutionary, effortlessly, easily, simply, super, leverage,
  unlock, game-changer, robust, cutting-edge.
- **No fabricated statistics.** If there is no real number, describe the
  direction qualitatively. Never write "in our testing" or "we measured"
  about something nobody measured. Never quote a Lua user count, MAU or
  revenue figure — none has been published.
- No claim that Lua tracks sales, revenue or conversions. See above.

## Opening paragraph

Three jobs: name the topic in plain language, state what the reader gets, and
anchor it to something concrete — a workflow, a header, a platform
constraint.

Works:

> A short link is a redirect with a memory. This article lists exactly what
> arrives with a click, what gets derived from it and how reliably, and what
> none of it can tell you — so the numbers on the dashboard mean something
> specific rather than something vague.

Does not work:

> Last week I shortened 200 links and learned a lot about analytics.

## Structure

- 1200 to 2500 words. Tutorials longer, opinion pieces tighter.
- 3 to 6 `##` sections. No `#` in the body — the frontmatter title is the H1.
- Sentence case headings.
- Short paragraphs, 3 to 4 sentences.
- Lists where a list fits. Do not pad prose with bullets.

**Narrative coherence (mandatory).** An article is one argument from open to
close, not four mini-articles under a shared title:

- The opening makes a specific promise. The conclusion delivers *that*
  promise, not one it drifted into.
- Each `##` exists because the section before it raised a question this one
  answers. Test: if two sections could swap order with nothing lost, they are
  not connected yet — merge or reorder them.
- The conclusion is not a recap of each heading. It answers "so what do I do
  with this" using everything the article built.

Read the draft start to finish as prose, ignoring the headings, before it is
done. If the conclusion answers a different question than the opening raised,
rewrite one of them.

## SEO checklist

- Title 50 to 60 characters, primary keyword near the start.
- Description under 155 characters, keyword in the first 100.
- First paragraph names the main topic.
- At least one `##` carries a keyword variation.
- At least one internal link to a Lua page.
- 2 to 5 tags, reusing existing ones where they fit.

## Where files live

```
resources/blog/<slug>.md          ← the article. The directory is the database.
public/images/blog/<slug>/
├── cover.jpg                     ← Unsplash cover
├── inline-1.jpg
└── inline-2.jpg
```

The slug is the filename and the URL. Nothing else to register.

## Frontmatter

```yaml
---
title: <50-60c, keyword early>
description: <under 155c>
date: YYYY-MM-DD
author: Lua
image: /images/blog/<slug>/cover.jpg
image_credit: Photo by <Name> on Unsplash (https://unsplash.com/photos/<id>)
tags: [tag-one, tag-two]
---
```

`date` is required — a post without one never publishes. A date in the future
is a scheduled draft: it stays out of the listing and 404s its own URL until
the day arrives. `draft: true` does the same thing indefinitely.

## Images from Unsplash

Three per article: one cover, two inline at roughly 1/3 and 2/3 of the body,
placed between sections rather than mid-paragraph.

Key: `UNSPLASH_ACCESS_KEY` in `.env`.

- Search `https://api.unsplash.com/search/photos`.
- Download the **`regular`** size (~1080px). Never `raw` or `full`.
- Avoid stock cliches: handshakes, lightbulbs, arrows pointing up, anyone in
  a headset pointing at a screen. Search concrete nouns tied to the article.
- Verify each file is over 10KB. Under that means the request failed.
- Credit the cover in `image_credit`.

## Review pipeline (mandatory, in this order)

A finished draft is not a saved draft:

1. **Narrative coherence** — the opening's promise and the conclusion's
   payoff match, and sections build on each other.
2. **Grammar and clarity** — read for correctness. Any sentence that needs a
   second read gets rewritten shorter.
3. **SEO checklist** — above.
4. **Humanizer** — run `.claude/skills/humanizer/SKILL.md` last, because a
   grammar or SEO edit after it undoes the polish.

Never skip one and never reorder them.
