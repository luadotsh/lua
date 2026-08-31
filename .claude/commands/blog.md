---
name: blog
description: |
  Write a complete, publish-ready blog post for Lua. Researches the topic,
  writes the article, fetches 3 Unsplash images (cover + 2 inline), runs a
  four-pass review (narrative coherence, grammar, SEO, humanizer) and saves
  to resources/blog/<slug>.md.
allowed-tools:
  - Read
  - Write
  - Edit
  - Grep
  - Glob
  - WebSearch
  - WebFetch
  - Bash
  - AskUserQuestion
---

# Blog article writer — Lua

One command: research, article, three images, four review passes.

## Mandatory reading before you start

1. `.claude/blog-context.md` — product, what the product does **not** do,
   audience, voice, banned words, file layout, Unsplash protocol, frontmatter.
   Non-negotiable.
2. `CLAUDE.md` — the project's own rules.
3. `.claude/skills/humanizer/SKILL.md` — you will apply this to the final
   draft.

## Input

A topic or working title, optionally target keywords and a slug hint. If the
user gives no topic, ask exactly once: "What's the topic for this post?"

## Process

### 1. Research

WebSearch the topic. Find current best practices, real limits, real numbers.
Skim what Bitly, Dub, Short.io and Rebrandly have published on it and find
what they miss or oversimplify — your angle goes there. Cite anything
concrete.

**Never invent a technical fact and never invent a statistic.** If there is
no real number, describe the direction qualitatively.

### 2. Outline (mental, not written)

- **Opening paragraph**: 3 to 4 sentences naming the topic, stating what the
  reader gets, and anchoring it to something concrete. No first-person
  anecdotal openers.
- **Body**: 3 to 6 `##` sections in a logical chain. Each one exists because
  the section before it raised a question this one answers.
- **Conclusion**: short. Delivers the exact promise the opening made, plus
  one natural next step.

### 3. Slug

Kebab-case, no accents, `[a-z0-9-]+` only — the route pattern rejects
anything else. It is the filename and the URL.

Check it is free: a post cannot take a slug another post already has.

```bash
ls resources/blog/
```

### 4. Write the article

Save to `resources/blog/<slug>.md`.

```yaml
---
title: <50-60 chars, keyword in the first half>
description: <under 155 chars>
date: <today, YYYY-MM-DD>
author: Lua
image: /images/blog/<slug>/cover.jpg
image_credit: Photo by <Name> on Unsplash (https://unsplash.com/photos/<id>)
tags: [<2 to 5 lowercase-hyphenated tags>]
---
```

Body rules are in `.claude/blog-context.md` — voice, banned words, paragraph
length, heading case, and the list of things Lua does not do. Two that get
broken most often:

- **Never claim Lua tracks conversions, sales or revenue.** It measures the
  click and stops there.
- **Never quote a Lua user count, MAU or revenue figure.** None is published.

Internal links: at least one, ideally two or three, to `/pricing`,
`/alternatives`, `/alternatives/<slug>` or another published post. Descriptive
anchor text, never "click here". External links only to authoritative sources
— official platform docs, RFCs, specs. No SEO content farms.

### 5. Narrative coherence pass

Read the draft start to finish as prose, ignoring the headings:

- Does the conclusion deliver the promise the opening made, or a different
  one it drifted into?
- Does each `##` follow from the one before it? If two could swap order with
  nothing lost, merge or reorder them.
- Does any section exist only to hit a word count? Cut it.

Fix the draft now, before spending effort on images and SEO.

### 6. Fetch 3 Unsplash images

```bash
mkdir -p public/images/blog/<slug>
echo $UNSPLASH_ACCESS_KEY
```

Search, then download the **`regular`** URL only (~1080px, never `raw` or
`full`):

```
https://api.unsplash.com/search/photos?query=<terms>&per_page=8&orientation=landscape&client_id=<KEY>
```

```bash
curl -sL "<regular_url>" -o public/images/blog/<slug>/cover.jpg
curl -sL "<regular_url>" -o public/images/blog/<slug>/inline-1.jpg
curl -sL "<regular_url>" -o public/images/blog/<slug>/inline-2.jpg
ls -la public/images/blog/<slug>/
```

Every file must be over 10KB; under that the request failed, so search again.

Search concrete nouns tied to the article, not generic ones. Avoid stock
cliches: handshakes, lightbulbs, arrows pointing up, anyone in a headset
pointing at a screen.

Place the two inline images between sections at roughly 1/3 and 2/3:

```markdown
![What the image actually shows](/images/blog/<slug>/inline-1.jpg)
```

Alt text describes the image. Do not keyword-stuff it.

### 7. Grammar and clarity pass

Read for correctness, not for style: spelling, punctuation, agreement,
run-on sentences, pronouns with no clear antecedent. Any sentence that needs
a second read gets rewritten shorter.

Before the SEO pass — a clean sentence is easier to judge for keyword
placement.

### 8. SEO pass

- Title 50 to 60 characters, keyword near the start.
- Description under 155 characters, keyword in the first 100.
- First paragraph names the main topic.
- At least one `##` carries a keyword variation.
- At least one internal link.
- 2 to 5 tags.

Fix anything that fails.

### 9. Humanizer pass (last)

Apply `.claude/skills/humanizer/SKILL.md` to the draft. Strip the AI writing
patterns, inflated significance, promotional language, rule-of-three padding
and vague attribution. Vary sentence rhythm.

Last, because a grammar or SEO edit after it undoes the polish. Never skip it.

### 10. Verify it actually publishes

The listing and the page are driven by the file, so check the file works
rather than assuming it does:

```bash
php artisan tinker --execute 'print_r(App\Actions\Blog\ListPosts::find("<slug>")["headings"]);'
php artisan test --compact tests/Feature/Site/BlogTest.php
```

A post dated in the future is a scheduled draft: it stays out of the listing
and 404s its own URL until the day arrives. That is intended — do not "fix"
it by backdating unless the user wanted it live today.

### 11. Report

Short message: slug, word count, internal links chosen, tags, what the
coherence pass rewrote, what the grammar pass caught, and anything the
humanizer flagged.

## Rules

- Never invent technical facts or statistics. WebSearch first.
- Never claim a feature Lua does not have — the list is in
  `.claude/blog-context.md`.
- Sentence case headings. No emojis in the body. No exclamation points.
- No banned words.
- Arrow functions only in any JS/TS example.
- The four passes are mandatory, in that exact order.

## Usage

```
/blog What a short link actually records
```

```
/blog
Topic: why UTM parameters beat the referrer header
Target keywords: utm parameters, referrer header
```
