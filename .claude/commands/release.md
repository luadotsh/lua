---
description: Friday release ritual — create git tag, GitHub release (auto-generated changelog), and a customer-facing email draft (Cal.com style)
allowed-tools: Bash, Write, Read, Skill
---

You are running the Friday release ritual for Lua. Four artifacts are produced:

1. A git tag (semver)
2. A GitHub release with the **auto-generated** changelog (PR list + authors via GitHub's native generator — flat, technical, for developers)
3. A **customer-facing email draft** in Cal.com style (themed prose, end-user voice, no commit/PR references)
4. A **changelog thumbnail** (1200×630 PNG) rendered from the email's headline and themes, using Lua's brand template

Plus local mirrors in `releases/<version>/` (changelog, email, and thumbnail), versioned via the artifacts PR.

**Always confirm with the user before any push/tag/release.**

## Context (auto-loaded)

- Current branch: !`git branch --show-current`
- Working tree: !`git status --porcelain`
- Latest tag: !`git describe --tags --abbrev=0 2>/dev/null || echo "(none)"`
- Repo (owner/name): !`gh repo view --json nameWithOwner -q .nameWithOwner 2>/dev/null || echo "(no gh)"`
- Local vs origin/main: !`git fetch --quiet origin main 2>/dev/null; git rev-list --left-right --count HEAD...origin/main 2>/dev/null || echo "0	0"`
- Commits since latest tag (or all if no tag): !`LAST_TAG=$(git describe --tags --abbrev=0 2>/dev/null); if [ -z "$LAST_TAG" ]; then git log --pretty=format:"%H%x09%s" --reverse; else git log "$LAST_TAG"..HEAD --pretty=format:"%H%x09%s" --reverse; fi`

## Workflow

### Step 1 — Pre-flight checks

Stop and tell the user if any of these fail:

- Current branch must be `main`. Else: ask user to `git checkout main`.
- Working tree must be clean. Else: ask user to commit/stash.
- Local in sync with `origin/main` (rev-list count `0	0`). Else: ask user to pull/push.
- Commits-since-tag list must be non-empty. Else: "Nothing new since the last tag."

### Step 2 — Determine next version

Lua uses **sequential numbering with rollover at 9** — not standard semver. Do not parse conventional commits to choose the bump. Every release is the next sequential number, whatever the commits look like.

1. If no previous tag exists → next version = **`v1.0.0`** (first release ever).
2. Otherwise, parse the latest tag as `vMAJOR.MINOR.PATCH` and increment by these rules:
   - `patch += 1`
   - If `patch` reaches `10`: set `patch = 0`, `minor += 1`
   - If `minor` reaches `10`: set `minor = 0`, `major += 1`
3. Re-prefix with `v`.

Examples:

| From | To |
|---|---|
| (no tag) | v1.0.0 |
| v1.0.0 | v1.0.1 |
| v1.0.8 | v1.0.9 |
| v1.0.9 | v1.1.0 |
| v1.5.7 | v1.5.8 |
| v1.9.8 | v1.9.9 |
| v1.9.9 | v2.0.0 |

There is no manual override — the next version is whatever the rule above produces. If a release needs a different version for some special reason, the user must create the tag manually outside this command.

### Step 3 — Preview the changelog (GitHub native format)

Use GitHub's release-notes generator API to produce the changelog **without creating anything yet**:

```bash
gh api -X POST "repos/{OWNER}/{REPO}/releases/generate-notes" \
  -f tag_name="<new_version>" \
  -f target_commitish="main" \
  -f previous_tag_name="<latest_tag>" \
  --jq '.body'
```

For the first release ever (no previous tag), omit the `previous_tag_name` flag — GitHub falls back to the initial commit.

The body already contains:
- `<subject> by @<author> in #<PR>` lines
- "New Contributors" section when applicable
- `Full Changelog: ...` compare link

**Do not modify it.** The GitHub-native format is the goal.

### Step 4 — Draft the customer email (Cal.com style)

This email is for **end users of Lua** — non-developers, paying customers, trial users. It must **NOT** reference: commits, PRs, authors, SHAs, conventional commit scopes, version control concepts, internal class names, file paths.

Read the commits only as **internal source material**. Translate to user-facing language.

**Every claim must be true of the product as it stands.** `.claude/blog-context.md` holds the list of what Lua does *not* do; the release email is bound by it exactly as the blog is. A changelog that credits the product with something it cannot do is worse than a dull one.

#### Structure

```markdown
---
subject: "Changelog <version> — <improvement 1>, <improvement 2> and more..."
---

# Changelog <version> — <improvement 1>, <improvement 2> and more...

By the Lua team • [Release <version>](https://github.com/<OWNER>/<REPO>/releases/tag/<version>)

Hello! Welcome to this week's update. Here's what's new in Lua.

## <Outcome the reader gets, or the symptom that is gone>

<2-4 sentences of concrete narrative — what changed, why a user should care, what they'll notice. Open on what the reader experienced ("you may have watched it go out twice"), not on the internal cause. No marketing puffery.>

## <second outcome>

<same>

## <third outcome — only if there are genuinely 3 themes worth of work>

<same>

## New features

- <user-facing one-liner — what they can now do>
- <...>

## Fixes

- <user-facing one-liner — what no longer breaks>
- <...>

Cheers,
Paulo from lua.sh

---

You're receiving this because you subscribed.
[Unsubscribe]({{unsubscribe_url}})
```

**Always link the GitHub release from the byline** — make `Release <version>` a link to `https://github.com/<OWNER>/<REPO>/releases/tag/<version>` (as shown above). It gives developer-minded readers the raw PR-level changelog without cluttering the body.

**Always end with the unsubscribe footer** — the `---` separator, the "You're receiving this because you subscribed." line, and an `[Unsubscribe]({{unsubscribe_url}})` link below the signature. Keep `{{unsubscribe_url}}` as a literal placeholder; the email sending tool fills it in. This footer is required on every customer email.

#### Section headers

A theme header names the outcome the reader gets or the symptom that is gone. Sentence case. It has to make sense to someone who never saw the bug report.

- ❌ `## Analytics that tell the whole story` (writerly; says nothing concrete)
- ❌ `## Your custom domains, everywhere` (area label dressed up)
- ✅ `## Your QR scans stop counting as clicks`
- ✅ `## The custom domains that wouldn't verify`

**Section order must match the subject order.** The subject promises a sequence. A reader who opens on a different topic than the one that got them to click feels the mismatch even if they can't name it.

#### Theme grouping (AI clusters by user impact)

Read all commits since the last tag and cluster into **2-3 user-facing themes**. Use whatever frame makes the changes feel coherent to a customer, not to a developer.

**Good themes** (end-user framing):
- "Links that survive a rename" — bundles custom-domain and slug work
- "Analytics you can act on" — bundles breakdown / timeseries improvements
- "QR codes worth printing" — bundles QR generation and scan tracking
- "Your own domain, faster" — bundles domain verification changes

**Bad themes** (internal framing — never use these):
- "Refactoring"
- "Dependency updates"
- "Feature commits" / "Fix commits"
- "Backend improvements"

If there are fewer than 3 themeable groups, use 2 or just 1. Don't pad. Internal-only changes (chore, CI, refactor, deps) usually shouldn't appear at all — fold the user-visible ones into "Fixes" with a user-voice rewrite, drop the rest.

**Order themes by reach, not by newness.** The first theme goes to whatever the largest share of subscribers will actually feel. A fix that unblocks a whole platform for everyone who uses it outranks a feature only integrators can reach, even though the feature is the newer work. Rank by how many people were affected.

**Check who a theme really reaches before you headline it.** Exposing an existing in-app capability over the API or MCP is new for integrators only — everyone else already had it in the UI. Headlining it as if the capability itself were new misleads the majority. Either qualify the section in its first sentence ("If you build against the Lua API or connect an AI assistant...") and say plainly that the in-app path already worked, or demote it under a theme with broader reach.

#### Bullet rules for "New features" / "Fixes"

Rewrite each item in **user voice**, not commit voice:

- ❌ "fix(analytics): use whereLike so search works on MySQL too"
- ✅ "Fixed link search returning nothing on self-hosted MySQL installs"

- ❌ "feat(billing): charge one-time trial setup fee at Stripe Checkout"
- ✅ (Probably its own theme, not a bullet — billing is a big user-facing topic)

- ❌ "chore(deps): bump axios to 1.13.5"
- ✅ (Skip entirely — pure internal)

If a commit has no user-visible effect, **omit it**. Don't pad the email.

**Never repeat a theme section in the bullets.** "New features" and "Fixes" are for what did *not* earn its own section. If miscounted QR scans got three paragraphs above, they don't also get a bullet — the reader meets the same fact twice and the lists stop being scannable. Write the sections first, then list only what is left over.

#### Subject line

Pattern: `Changelog <version> — <improvement 1>, <improvement 2> and more...`

Each slot must name **what got better for the reader**, never the area it happened in. The area is where the work landed; the improvement is what they can now do, or what stopped hurting. A subject built from area labels tells a customer nothing — they already know Lua shortens links.

- ❌ `Changelog v1.0.8 — Analytics that tell the story, Custom domains, QR codes` (a writerly abstraction plus two bare area labels)
- ❌ `Changelog v1.0.8 — No more miscounted scans, Custom domains that verify` (still abstract: "no more miscounted scans" reads like a new feature rather than a fix, and the second half is awkward)
- ✅ `Changelog v1.0.8 — Custom domains finally verify, see scans apart from clicks and more...`

Rules:

- **Two named improvements plus `and more...` beats three cramped ones.** The trailing `and more...` carries the rest of the release and buys the two named slots enough room to be specific.
- **The first slot goes to the widest reach.** Same ranking as the themes: most subscribers affected wins, even when that is a bug fix and the release also shipped a shiny feature.
- Plain words like `finally`, `stop`, `no longer` are good — they read as an honest founder, not a marketer.
- Don't put "Lua" in the subject — the email already comes from the Lua sender, so it's redundant.
- Cap around 80 chars.

### Step 5 — Humanize the email prose

Run the email body through the `humanizer` skill before previewing:

1. Invoke the `Skill` tool with `skill: humanizer` and pass the draft email body plus this context: *"This is a customer-facing changelog email for Lua (open-source link shortener with analytics). Tone: developer founder writing to early users on a Friday — warm, specific, no marketing puffery. Cal.com style. Keep the existing structure (subject frontmatter, section headers, bullets, signature, unsubscribe footer). Do not strip section headers, the 'Cheers, Paulo from lua.sh' signature, or the unsubscribe footer."*
2. Replace the draft email body with the humanized version.

**Do NOT humanize:**
- The changelog from Step 3 (flat commit list, no prose).
- The subject line frontmatter.
- The literal signature `Cheers,\nPaulo from lua.sh` — keep it exact.
- The unsubscribe footer (`---`, "You're receiving this because you subscribed.", `[Unsubscribe]({{unsubscribe_url}})`) — keep it exact, below the signature.

The humanizer skill itself covers all patterns. Trust it.

### Step 5b — Render the changelog thumbnail

Derive these inputs from the release. The **headline and the chips play different roles — never make one restate the other**:

- **version** — always pass the release version (e.g. `v1.0.6`). It is stamped in the badge as a mono segment (`★ CHANGELOG | v1.0.6`) so every release image is consistent. Not optional.
- **headline** — a crafted marketing hero line (2-6 words, at most two lines), in the voice of the marketing site's hero. It sells the *benefit* of the release's biggest wins; it is the loudest thing on the image. Do **NOT** paste the subject line, and do **NOT** just list the theme/chip words — the chips already name the areas, so the headline sits one level above them. Read the email's themes **and** the "New features" bullets, find the strongest story, and write a fresh benefit line. A little rhythm helps (a parallel pair reads well, e.g. `Speak every language, reach every reader`). Sentence case, no version number in the headline itself (it lives in the badge), no period.
  - ❌ `Your language, mobile, and per-image alt text` (this is just the chip labels)
  - ✅ `Speak every language, reach every reader` (benefit-driven, distinct from the chips)
- **underline** — a short emphasis phrase *inside* the headline (1-3 words) to carry the hand-drawn accent squiggle, usually the last / most important phrase (e.g. `every reader`). Must appear in the headline verbatim. Optional; omit for no squiggle.
- **themes** — 2-4 short chip labels naming the concrete areas that shipped, condensed to 1-2 words each (e.g. `Languages`, `Mobile`, `Alt text & previews`). These are secondary supporting labels, set in the small mono `.label` register; the template gives the **first** chip the accent and leaves the rest neutral, so put the widest-reaching theme first. Keep them concrete and distinct from the headline's wording. **Order the chips to match the email's section order**, so the image and the email tell the story in the same sequence. Chips are the one place a bare area label is correct: the headline sells the benefit, the chips name where it landed.

Create the directory and render the thumbnail so the user can preview it before confirming:

```bash
mkdir -p releases/<version>
node .claude/release-assets/render-thumbnail.mjs \
  --version "<version>" \
  --headline "<headline>" \
  --underline "<emphasis phrase>" \
  --themes "<label 1>,<label 2>,<label 3>" \
  --out releases/<version>/thumbnail.png
```

This uses the shared brand template (`.claude/release-assets/thumbnail.template.html`) and the app's own `public/images/lua/full-black.svg`, so the wordmark cannot drift from the one the site ships. It is built on the marketing site's devices rather than a separate look: the ruled column (a hairline at each container edge), a masked dot grid, a Schibsted Grotesk headline set at 700 with tight tracking on pure black, a hand-drawn Heat squiggle under the emphasis phrase, a Heat "Changelog" badge with the version stamped beside it in mono, and mono uppercase theme chips. 1200×630, rendered at 2x. It needs Playwright + chromium (already installed for browser tests). If the render fails, report and stop before tagging.

### Step 6 — Confirm with the user

Show:
1. **Proposed version** (e.g., `v1.0.9 → v1.1.0` — sequential rollover at 9).
2. **Changelog preview** (Step 3 output).
3. **Email preview**: subject line + full body (post-humanizer).
4. **Thumbnail**: `releases/<version>/thumbnail.png` (already rendered in Step 5b — tell the user they can open it to preview).
5. **Files that will be created/pushed**:
   - Tag `<version>` (pushed to origin)
   - GitHub release `<version>`
   - `releases/<version>/changelog.md`
   - `releases/<version>/email.md`
   - `releases/<version>/thumbnail.png`
   - Branch `chore/release-<version>-artifacts` + a PR versioning the three files above

Then ask: **"Create the tag, publish the release, and open the PR with the artifacts?"**

Do **not** proceed without explicit yes.

### Step 7 — Execute

After confirmation, in this exact order. Steps 4–6 (tag + release) run from `main` so the tag stays on the released `main` commit; steps 7–8 version the artifacts on a separate branch and open a PR.

1. Create local directory: `mkdir -p releases/<version>` (already created in Step 5b).
2. Write `releases/<version>/changelog.md` with the Step 3 content (raw GitHub markdown).
3. Write `releases/<version>/email.md` with frontmatter + humanized body.
   (`releases/<version>/thumbnail.png` was already rendered in Step 5b.)
4. From `main`, create the annotated tag: `git tag -a <version> -m "Release <version>"`
5. Push tag: `git push origin <version>`
6. Create the GitHub release using the changelog file as body:
   ```bash
   gh release create <version> --title "<version>" --notes-file releases/<version>/changelog.md
   ```
7. Version the artifacts on a branch (the three files carry over from the working tree) and push:
   ```bash
   git checkout -b chore/release-<version>-artifacts
   git add releases/<version>/changelog.md releases/<version>/email.md releases/<version>/thumbnail.png
   git commit -m "chore(release): add <version> changelog, customer email, and thumbnail artifacts"
   git push -u origin chore/release-<version>-artifacts
   ```
8. Open the PR against `main` (body: what the three files are + a link to the GitHub release):
   ```bash
   gh pr create --base main --head chore/release-<version>-artifacts \
     --title "chore(release): <version> changelog, customer email + thumbnail artifacts" \
     --body "<short body — the three artifact files + link to the release>"
   ```
9. Report to the user:
   - GitHub release URL (from `gh release` output)
   - PR URL (from `gh pr create` output)
   - Local paths: `releases/<version>/changelog.md`, `releases/<version>/email.md`, `releases/<version>/thumbnail.png`

### On failure

- `git push origin <version>` fails: report the exact error, leave the local tag in place, do not retry destructively.
- `gh release create` fails: the tag is already pushed; tell the user they can recreate manually with `gh release create <version> --title "<version>" --notes-file releases/<version>/changelog.md`.
- `git push` / `gh pr create` for the artifacts branch fails: the tag and GitHub release are already live; report the error and tell the user they can open the PR manually from `chore/release-<version>-artifacts`.
- `Skill`, `Write`, or thumbnail render failure during artifact prep: report and stop. Do not push the tag without the artifacts being prepared.
