# CI/CD, Docker release, and the `/release` ritual

**Date:** 2026-08-31
**Status:** approved for planning
**Reference implementation:** `~/Herd/trypost`

## Why

The CI is red and has been for some time. Every recent run fails — both
dependency PRs and all three Dependabot PRs — and PR #122 was merged over a red
check. Nobody is being protected by it.

The cause is a single missing line. `phpunit.xml:41` pins
`DB_CONNECTION=pgsql`, and `backend-tests.yml` starts **only** a MySQL service
without ever setting `DB_CONNECTION`. The Postgres driver opens a socket to the
MySQL port, reads a protocol handshake it cannot parse, and dies:

```
SQLSTATE[08006] connection to server at "127.0.0.1", port 32768 failed:
received invalid response to SSL negotiation: J (Connection: pgsql, ...)
Tests: 510 failed, 25 passed
```

The 25 that pass are the ones that never touch the database.

Alongside that, the project has no Docker image, no release process, and no way
to hand a self-hoster something to run. TryPost has all three, working, and this
spec ports that arrangement rather than inventing a second one.

## Decisions taken

| Question | Decision |
| --- | --- |
| Database coverage | Matrix on **both** PostgreSQL and MySQL, with a job-gate requiring both |
| MySQL failures | Fixed in this same PR |
| Lint | Bring eslint + prettier + the workflow, including the new dev dependencies |
| Delivery | One PR, organised into reviewable commits |
| `/release` | Full ritual, rebranded to Lua |
| Husky | Removed, and commitlint goes with it |

## A. Test workflow

### `.github/actions/setup-laravel/action.yml`

A composite action, ported from TryPost, with three deliberate differences:

- **PHP 8.5**, not 8.4 — `composer.json` requires `^8.5`.
- Extensions carry **both** `pdo_pgsql` and `pdo_mysql`, since the matrix needs
  either driver from the same image.
- Keeps the `passport:keys --force` step; Lua uses Passport for its API.

Inputs: `node` (default `false`), `db-port`, `redis-port`.

### `.github/workflows/tests.yml`

Replaces `backend-tests.yml`.

- Matrix: `pgsql` on `postgres:16`, `mysql` on `mysql:8.4`.
- **`DB_CONNECTION` comes from the matrix.** This is the line whose absence
  causes the current failure. A job-level env var beats `phpunit.xml` — verified
  against TryPost, whose matrix is green on `main` in about three minutes.
- A `backend` gate job requiring every matrix leg to pass. This becomes the
  required check on `main`.
- A separate `e2e` job: build assets, install Chromium, run `tests/Browser`,
  upload screenshots on failure. Postgres only — the browser suite does not vary
  by engine and doubling it buys nothing.
- `php artisan test --compact --parallel`.
- Triggers on `push` to `main` **and** `pull_request`. Today it runs on PRs
  alone, so `main` carries no signal at all.
- Action versions move to checkout@v7, cache@v6, upload-artifact@v7.

### The MySQL failures

Running the suite against local MySQL 9.4 gives **533 passed, 2 failed**. Both
failures are in `AnalyticsActionsTest` (`:99` and `:213`), and both are the same
root cause:

```sql
SELECT CONVERT_TZ('2026-08-31 12:00:00','+00:00','UTC');    -- NULL
SELECT CONVERT_TZ('2026-08-31 12:00:00','+00:00','+00:00'); -- works
SELECT COUNT(*) FROM mysql.time_zone_name;                  -- 0
```

`GetTimeseries::bucketExpression()` already handles MySQL correctly — it swaps
`date_trunc` for `DATE_FORMAT` + `CONVERT_TZ`, exactly as the CLAUDE.md database
section prescribes. But `CONVERT_TZ` returns `NULL` for a **named** zone when
the server's timezone tables are empty. A null bucket matches no row, so the
series sums to zero and the assertion fails.

This is a product bug, not a test bug: a self-hosted MySQL install without
`mysql_tzinfo_to_sql` loaded reports **zero analytics, silently**. The MySQL leg
paid for itself the first time it ran.

**Fix:** load the timezone tables into the MySQL service before the suite runs.
No application code changes.

```bash
mysql_tzinfo_to_sql /usr/share/zoneinfo | mysql -h 127.0.0.1 -u root -ppassword mysql
```

The same requirement must be stated for self-hosters — see section C.

## B. Lint

New dev dependencies, matching TryPost's set: `eslint`, `@eslint/js`,
`typescript-eslint`, `eslint-plugin-vue`, `@vue/eslint-config-typescript`,
`eslint-config-prettier`, `eslint-plugin-import`,
`eslint-import-resolver-typescript`, `prettier`,
`prettier-plugin-organize-imports`, `prettier-plugin-tailwindcss`.

New files: `eslint.config.js`, `.prettierrc`, `.prettierignore`.

New scripts — `format`, `format:check`, `lint` in `package.json`; `lint`,
`test:lint`, `test`, `test:all` in `composer.json`, with `lint` running
`pint --parallel`.

`.github/workflows/lint.yml` runs Pint, `format:check` and `lint`.

> The first `prettier --write resources/` reformats most of the frontend. That
> lands as its own commit, so the rest of the PR stays reviewable. The workflow
> uses `format:check` rather than TryPost's `format`, so CI reports drift
> instead of silently rewriting files it does not commit.

## C. Docker

`docker/` does not exist in Lua and is created whole: `Dockerfile`,
`entrypoint.sh`, `nginx.conf`, `supervisord.dev.conf`, `supervisord.prod.conf`,
`php.dev.ini`, `php.prod.ini`, `.env.docker.example`, plus a root
`.dockerignore`, `compose.yaml` and `compose.prod.yaml`.

The Dockerfile keeps TryPost's stage layout: `system-base` → `composer-deps` /
`composer-deps-prod` → `asset-build` → `dev` / `production`. PHP and Node share
the asset stage because the Wayfinder Vite plugin shells out to
`php artisan wayfinder:generate` during the build.

Four adaptations are **not** copy-paste:

1. **Inertia SSR.** CLAUDE.md is explicit that SSR runs in production and that a
   crashed SSR process fails silently — pages fall back to client rendering and
   look fine. `supervisord.prod.conf` therefore gets an `inertia-ssr` program
   running `php artisan inertia:start-ssr`. TryPost has no such program; copying
   its supervisord verbatim would ship an image whose SSR never starts and whose
   failure mode is invisible.
2. **Vite build args** become Lua's: `VITE_REVERB_*` and the PostHog trio. Vite
   inlines these at build time, so runtime env vars cannot fix a wrong value.
3. **Entrypoint waits for Postgres *or* MySQL**, since Lua supports both.
   TryPost's entrypoint hardcodes `pg_isready`.
4. **MySQL timezone tables.** `.env.docker.example` and the compose files must
   document that a MySQL-backed install needs `mysql_tzinfo_to_sql` loaded, for
   the reason established in section A.

The existing root `docker-compose.yml` is Sail's, and it is broken: it builds
`vendor/laravel/sail/runtimes/8.2` while `composer.json` requires PHP `^8.5`, so
`composer install` inside that container cannot succeed. It is replaced by the
new compose files.

### `.github/workflows/release-docker.yml`

Ported essentially unchanged. On a `v*.*.*` tag: build `linux/amd64` on
`ubuntu-latest` and `linux/arm64` on `ubuntu-24.04-arm` — native runners, no
QEMU — push each by digest, then stitch one multi-arch manifest into GHCR with
semver tags plus `latest`.

## D. The `/release` command

`.claude/commands/release.md` and `.claude/release-assets/` are ported and
rebranded:

- Version scheme unchanged: **sequential with rollover at 9**, not semver
  bumping from conventional commits.
- Changelog unchanged: GitHub's native `generate-notes` output, unmodified.
- Customer email keeps the Cal.com structure and the humanizer pass; the voice,
  signature and product claims become Lua's.
- Thumbnail template is rebuilt on Lua's design tokens — Heat `#FA5D19`,
  Schibsted Grotesk display, the `full-black.svg` wordmark — instead of
  TryPost's cream/lavender wash.
- Artifacts land in `releases/<version>/` as they do in TryPost.

The tag this command pushes is what triggers section C's image build, so the two
halves are one release: tag → GitHub release → GHCR image.

## E. Removing Husky

Deleted: `.husky/`, the `husky` dev dependency, the `prepare` script, and the
`git config core.hooksPath .husky` line in `composer.json`'s
`post-autoload-dump`.

`commitlint` goes with it. The `commit-msg` hook was its only caller, so
`@commitlint/cli`, `@commitlint/config-conventional` and `.commitlintrc.json`
would be dead weight. Removing them also retires Dependabot PR #118 and drops
the nine transitive `lodash.*` packages, which existed solely to serve
commitlint.

`.husky/pre-commit` is already empty apart from a commented-out
`# php artisan test`, so nothing of substance is lost.

## Testing

- Full suite green on PostgreSQL **and** MySQL locally before the PR opens.
- `tests/Browser` green after `npm run build`.
- `vue-tsc --noEmit` clean, and `npm run lint` / `format:check` clean.
- The Docker image is built locally for both targets and booted, confirming:
  migrations run, `/up` answers, and the SSR process is alive (server-rendered
  markup present, not a client-render fallback).
- Workflow YAML parses; the matrix legs are asserted to actually select
  different `DB_CONNECTION` values rather than silently sharing one.

## Risks

- **Prettier's first pass is large.** Isolated in its own commit.
- **The image is unverifiable in CI on the first tag.** The build is exercised
  locally before merge; `workflow_dispatch` stays on the release workflow so it
  can be run without cutting a tag.
- **A required check cannot be set from this PR.** After merge, `backend` has to
  be marked required on `main` in repository settings — otherwise red merges
  stay possible, which is the problem that started this.

## Out of scope

- Deploying anywhere. This publishes an image; it does not run it.
- Reverb, which stays as-is per the decision on 2026-08-31: broadcasting is
  wired but unused, and real-time work is coming.
- The remaining major upgrades (typescript 7, vue-tsc 3, @vueuse/core 14),
  tracked separately.
