# CI/CD, Docker Release and `/release` Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Turn a permanently-red CI into a two-engine test matrix that gates merges, and give the project a Docker image published on every release tag plus a `/release` command to cut one.

**Architecture:** Port TryPost's arrangement rather than invent a second one. A composite action carries the shared setup; a matrix runs the suite on PostgreSQL and MySQL behind a single gate job; a multi-stage Dockerfile builds `dev` and `production` targets; a tag triggers a native-runner multi-arch build into GHCR; `/release` cuts the tag.

**Tech Stack:** GitHub Actions, PHP 8.5, Laravel 13, Pest 5, Playwright, Docker Buildx, nginx + php-fpm + supervisor on Alpine, ESLint 9 + Prettier 3.

**Spec:** `docs/superpowers/specs/2026-08-31-cicd-release-docker-design.md`

**Reference implementation:** `~/Herd/trypost` — a working copy of this arrangement. Read the corresponding file there before writing each ported one.

## Global Constraints

- **PHP is `^8.5`.** TryPost pins 8.4 everywhere; every ported file must say 8.5.
- **Both database engines must pass.** A feature that only works on PostgreSQL is a feature Lua does not have.
- **Named routes in tests**, never hardcoded URLs. `data-testid` for browser selectors, never CSS classes.
- **No `Co-Authored-By` lines in commit messages.**
- **Do not commit, push or open a PR until every task is done** and the full suite is green on both engines.
- **Arrow functions only** in TypeScript and Vue.
- Commit messages use `chore:` / `fix:` / `feat:` prefixes with a lowercase subject.
- Branch: `chore/cicd-release-docker`, cut from `main`.

---

### Task 1: Remove Husky and commitlint

Husky is being removed by explicit request; commitlint's only caller was the `commit-msg` hook, so it goes too.

**Files:**
- Delete: `.husky/` (whole directory), `.commitlintrc.json`
- Modify: `package.json` (drop `husky`, `@commitlint/cli`, `@commitlint/config-conventional`, and the `prepare` script)
- Modify: `composer.json:67` (drop the `core.hooksPath` line from `post-autoload-dump`)

- [ ] **Step 1: Create the branch**

```bash
git checkout main && git pull
git checkout -b chore/cicd-release-docker
```

- [ ] **Step 2: Remove the packages and the hook directory**

```bash
npm uninstall husky @commitlint/cli @commitlint/config-conventional
rm -rf .husky .commitlintrc.json
```

- [ ] **Step 3: Drop the `prepare` script**

In `package.json`, remove the line `"prepare": "husky"` from `scripts`. The `scripts` block should be left with `build` and `dev` only.

- [ ] **Step 4: Drop the hooksPath line from composer.json**

In `composer.json`, `post-autoload-dump` currently reads:

```json
"post-autoload-dump": [
    "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump",
    "@php artisan package:discover --ansi",
    "@php -r \"if (shell_exec('command -v git')) { shell_exec('git config core.hooksPath .husky'); }\""
],
```

Remove the third entry, leaving the first two.

- [ ] **Step 5: Unset the hooks path on the local clone**

The setting is stored in `.git/config`, so removing the directory alone leaves git pointing at a path that no longer exists.

```bash
git config --unset core.hooksPath || true
git config --get core.hooksPath && echo "STILL SET — investigate" || echo "unset, as expected"
```

- [ ] **Step 6: Verify nothing still references husky or commitlint**

```bash
grep -rn "husky\|commitlint" --exclude-dir=node_modules --exclude-dir=vendor \
  --exclude=package-lock.json --exclude-dir=.git . || echo "clean"
```
Expected: `clean`

- [ ] **Step 7: Verify a commit still works**

```bash
git add -A
git commit -m "chore: remove husky and commitlint"
```
Expected: the commit succeeds with no hook output. Previously the `commit-msg` hook printed commitlint results.

---

### Task 2: The composite setup action

Everything downstream depends on this. It is used by both jobs in Task 3.

**Files:**
- Create: `.github/actions/setup-laravel/action.yml`

**Interfaces:**
- Produces: an action at `./.github/actions/setup-laravel` taking inputs `node` (`'true'`/`'false'`, default `'false'`), `db-port` (required), `redis-port` (required). Task 3 consumes exactly these three names.

- [ ] **Step 1: Read the reference**

```bash
cat ~/Herd/trypost/.github/actions/setup-laravel/action.yml
```

- [ ] **Step 2: Write the action**

Create `.github/actions/setup-laravel/action.yml`:

```yaml
name: 'Setup Laravel test environment'
description: 'Set up PHP, Composer, optional Node, the app key, database, and Passport keys for a test job.'

inputs:
  node:
    description: 'Also set up Node.js and install npm dependencies.'
    default: 'false'
  db-port:
    description: 'Host port mapped to the database service.'
    required: true
  redis-port:
    description: 'Host port mapped to the Redis service.'
    required: true

runs:
  using: 'composite'
  steps:
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.5'
        extensions: dom, curl, libxml, mbstring, zip, pcntl, pdo, pdo_pgsql, pdo_mysql, bcmath, intl, gd, redis
        coverage: none

    - name: Setup Node.js
      if: inputs.node == 'true'
      uses: actions/setup-node@v6
      with:
        node-version: '22'
        cache: 'npm'

    - name: Cache Composer dependencies
      uses: actions/cache@v6
      with:
        path: vendor
        key: ${{ runner.os }}-composer-${{ hashFiles('**/composer.lock') }}
        restore-keys: ${{ runner.os }}-composer-

    - name: Prepare environment
      shell: bash
      run: cp .env.ci .env

    - name: Install Composer dependencies
      shell: bash
      run: composer install --no-interaction --prefer-dist --optimize-autoloader

    - name: Install npm dependencies
      if: inputs.node == 'true'
      shell: bash
      run: npm ci

    - name: Generate application key
      shell: bash
      run: php artisan key:generate

    - name: Run migrations
      shell: bash
      env:
        DB_PORT: ${{ inputs.db-port }}
        REDIS_PORT: ${{ inputs.redis-port }}
      run: php artisan migrate --force

    - name: Generate Passport keys
      shell: bash
      run: php artisan passport:keys --force
```

Note the two deliberate differences from TryPost: `php-version` is `8.5` (TryPost has 8.4), and the extension list carries **both** `pdo_pgsql` and `pdo_mysql` so one image serves both matrix legs.

- [ ] **Step 3: Commit**

```bash
git add .github/actions/setup-laravel/action.yml
git commit -m "chore: add the composite action the test jobs share"
```

---

### Task 3: The test matrix — the fix for the red CI

This is the task that turns CI green. The failure today is that `phpunit.xml:41` pins `DB_CONNECTION=pgsql` while the workflow starts only MySQL and never sets `DB_CONNECTION`; a job-level env var wins over `phpunit.xml`, so setting it from the matrix is the whole fix.

**Files:**
- Create: `.github/workflows/tests.yml`
- Delete: `.github/workflows/backend-tests.yml`

**Interfaces:**
- Consumes: `./.github/actions/setup-laravel` from Task 2, with inputs `node`, `db-port`, `redis-port`.
- Produces: a job named `backend` — this is the name to mark as the required check after merge.

- [ ] **Step 1: Confirm the failure exists before fixing it**

```bash
gh run list --limit 3
```
Expected: recent runs show `failure`. Confirm with:

```bash
gh run view --log-failed 2>&1 | grep -m1 "SQLSTATE\|Tests:"
```
Expected: a `SQLSTATE[08006]` Postgres connection error, and a `Tests: 510 failed, 25 passed` line.

- [ ] **Step 2: Write the workflow**

Create `.github/workflows/tests.yml`:

```yaml
name: Tests

on:
  push:
    branches: [main]
  pull_request:
    branches: [main]

env:
  DB_DATABASE: lua_test
  DB_PASSWORD: password
  BROADCAST_CONNECTION: log
  CACHE_STORE: array
  QUEUE_CONNECTION: sync
  SESSION_DRIVER: array

jobs:
  tests:
    name: Tests (${{ matrix.name }})
    runs-on: ubuntu-latest

    strategy:
      fail-fast: false
      matrix:
        include:
          - name: PostgreSQL
            connection: pgsql
            image: postgres:16
            port: '5432'
            username: postgres
            health: '--health-cmd="pg_isready"'
          - name: MySQL
            connection: mysql
            image: mysql:8.4
            port: '3306'
            username: root
            health: '--health-cmd="mysqladmin ping -h 127.0.0.1 -u root -ppassword"'

    env:
      DB_CONNECTION: ${{ matrix.connection }}
      DB_USERNAME: ${{ matrix.username }}

    services:
      database:
        image: ${{ matrix.image }}
        env:
          POSTGRES_USER: postgres
          POSTGRES_PASSWORD: password
          POSTGRES_DB: lua_test
          MYSQL_ROOT_PASSWORD: password
          MYSQL_DATABASE: lua_test
        ports:
          - ${{ matrix.port }}/tcp
        options: >-
          ${{ matrix.health }}
          --health-interval=5s
          --health-timeout=5s
          --health-retries=20

      redis:
        image: redis:7
        ports:
          - 6379/tcp
        options: >-
          --health-cmd="redis-cli ping"
          --health-interval=10s
          --health-timeout=5s
          --health-retries=3

    steps:
      - name: Checkout code
        uses: actions/checkout@v7

      # CONVERT_TZ returns NULL for a named zone unless the server's timezone
      # tables are populated, which silently zeroes every analytics timeseries.
      # See GetTimeseries::bucketExpression().
      # Run inside the service container, not on the runner: mysql_tzinfo_to_sql
      # ships with mysql-server, and the runner only has the client.
      - name: Load MySQL timezone tables
        if: matrix.connection == 'mysql'
        run: |
          CID=$(docker ps --filter "ancestor=${{ matrix.image }}" --format '{{.ID}}' | head -1)
          test -n "$CID" || { echo "mysql service container not found"; exit 1; }
          docker exec "$CID" sh -c \
            "mysql_tzinfo_to_sql /usr/share/zoneinfo | mysql -u root -ppassword mysql" 2>/dev/null
          docker exec "$CID" mysql -u root -ppassword \
            -e "SELECT COUNT(*) AS tz_rows FROM mysql.time_zone_name;"

      - name: Setup test environment
        uses: ./.github/actions/setup-laravel
        with:
          db-port: ${{ job.services.database.ports[matrix.port] }}
          redis-port: ${{ job.services.redis.ports['6379'] }}

      - name: Run backend tests
        env:
          DB_PORT: ${{ job.services.database.ports[matrix.port] }}
          REDIS_PORT: ${{ job.services.redis.ports['6379'] }}
        run: php artisan test --compact --parallel

  backend:
    name: backend
    runs-on: ubuntu-latest
    needs: [tests]
    if: always()

    steps:
      - name: Fail unless every engine passed
        run: '[ "${{ needs.tests.result }}" = "success" ]'

  e2e:
    runs-on: ubuntu-latest

    env:
      DB_CONNECTION: pgsql
      DB_USERNAME: postgres

    services:
      postgres:
        image: postgres:16
        env:
          POSTGRES_USER: postgres
          POSTGRES_PASSWORD: password
          POSTGRES_DB: lua_test
        ports:
          - 5432/tcp
        options: >-
          --health-cmd="pg_isready"
          --health-interval=10s
          --health-timeout=5s
          --health-retries=3

      redis:
        image: redis:7
        ports:
          - 6379/tcp
        options: >-
          --health-cmd="redis-cli ping"
          --health-interval=10s
          --health-timeout=5s
          --health-retries=3

    steps:
      - name: Checkout code
        uses: actions/checkout@v7

      - name: Setup test environment
        uses: ./.github/actions/setup-laravel
        with:
          node: 'true'
          db-port: ${{ job.services.postgres.ports['5432'] }}
          redis-port: ${{ job.services.redis.ports['6379'] }}

      - name: Build assets
        run: npm run build

      - name: Install Playwright browsers
        run: npx playwright install --with-deps chromium

      - name: Run browser tests
        env:
          DB_PORT: ${{ job.services.postgres.ports['5432'] }}
          REDIS_PORT: ${{ job.services.redis.ports['6379'] }}
        run: php artisan test tests/Browser --compact

      - name: Upload Playwright artifacts
        if: failure()
        uses: actions/upload-artifact@v7
        with:
          name: playwright-artifacts
          path: tests/Browser/Screenshots
          if-no-files-found: ignore
          retention-days: 7
```

- [ ] **Step 3: Delete the old workflow**

```bash
git rm .github/workflows/backend-tests.yml
```

- [ ] **Step 4: Prove the MySQL leg passes locally, before trusting CI**

The two known MySQL failures are `AnalyticsActionsTest:99` and `:213`. Reproduce them first:

```bash
mysql -h 127.0.0.1 -P 3306 -u root -e "CREATE DATABASE IF NOT EXISTS lua_test;"
DB_CONNECTION=mysql DB_HOST=127.0.0.1 DB_PORT=3306 DB_USERNAME=root DB_PASSWORD= DB_DATABASE=lua_test \
  php artisan test --compact --filter="AnalyticsActionsTest"
```
Expected: 2 failed. Confirm the cause is the timezone tables, not application code:

```bash
mysql -h 127.0.0.1 -P 3306 -u root -e "
  SELECT CONVERT_TZ('2026-08-31 12:00:00','+00:00','UTC') AS named_zone;
  SELECT COUNT(*) AS tz_rows FROM mysql.time_zone_name;"
```
Expected: `named_zone` is `NULL` and `tz_rows` is `0`.

- [ ] **Step 5: Load the timezone tables locally and re-run**

```bash
mysql_tzinfo_to_sql /usr/share/zoneinfo 2>/dev/null | mysql -h 127.0.0.1 -u root mysql
DB_CONNECTION=mysql DB_HOST=127.0.0.1 DB_PORT=3306 DB_USERNAME=root DB_PASSWORD= DB_DATABASE=lua_test \
  php artisan test --compact
```
Expected: **535 passed**. If any test still fails, it is a genuine MySQL portability bug — consult the "Database engines" section of `CLAUDE.md` (JSON key order, the 2038 `timestamp()` ceiling, `whereLike`, `count(case when ...)`) and fix the application code rather than the test's expectation.

> On macOS `mysql_tzinfo_to_sql` may not ship with Herd's MySQL. If it is missing, load the tables from the system zoneinfo with the same command run inside a `mysql:8.4` container, or accept that the two analytics tests are skipped locally and rely on the CI leg — but do not change `GetTimeseries`.

- [ ] **Step 6: Confirm PostgreSQL still passes**

```bash
php artisan test --compact
```
Expected: 535 passed.

- [ ] **Step 7: Commit**

```bash
git add .github/workflows/tests.yml
git commit -m "fix: run the suite on both engines and set the connection the matrix picks"
```

---

### Task 4: Lint tooling

**Files:**
- Create: `eslint.config.js`, `.prettierrc`, `.prettierignore`, `.github/workflows/lint.yml`
- Modify: `package.json` (dev deps + `format`, `format:check`, `lint` scripts), `composer.json` (`lint`, `test:lint`, `test`, `test:all` scripts)

- [ ] **Step 1: Install the dev dependencies**

```bash
npm install -D eslint @eslint/js typescript-eslint eslint-plugin-vue \
  @vue/eslint-config-typescript eslint-config-prettier eslint-plugin-import \
  eslint-import-resolver-typescript prettier prettier-plugin-organize-imports \
  prettier-plugin-tailwindcss
```

- [ ] **Step 2: Copy the configs from the reference**

```bash
cp ~/Herd/trypost/eslint.config.js ./eslint.config.js
cp ~/Herd/trypost/.prettierrc ./.prettierrc
cp ~/Herd/trypost/.prettierignore ./.prettierignore
```

Then read `eslint.config.js` and adjust any path globs that name TryPost-only directories. `.prettierignore` should read:

```
resources/js/components/ui/*
```

TryPost's second line (`resources/views/mail/*`) only applies if Lua has that directory — check with `ls resources/views/mail` and keep the line only if it exists.

- [ ] **Step 3: Add the npm scripts**

In `package.json`, the `scripts` block becomes:

```json
"scripts": {
    "build": "vite build && vite build --ssr",
    "dev": "vite",
    "format": "prettier --write resources/",
    "format:check": "prettier --check resources/",
    "lint": "eslint . --fix"
}
```

- [ ] **Step 4: Add the composer scripts**

In `composer.json`, add to `scripts`:

```json
"lint": ["pint --parallel"],
"test:lint": ["pint --parallel --test"],
"test": ["@php artisan config:clear --ansi", "@test:lint", "@php artisan test"],
"test:all": ["@test", "@php artisan test tests/Browser"]
```

- [ ] **Step 5: Commit the tooling before running it**

Keeping the config and the reformat in separate commits is what makes the PR reviewable.

```bash
git add package.json package-lock.json composer.json eslint.config.js .prettierrc .prettierignore
git commit -m "chore: add eslint and prettier"
```

- [ ] **Step 6: Run the formatter — its own commit**

```bash
npm run format
npm run lint
```

- [ ] **Step 7: Verify the reformat broke nothing**

```bash
npx vue-tsc --noEmit
npm run build
php artisan test --compact
```
Expected: typecheck clean, build succeeds, 535 passed.

- [ ] **Step 8: Commit the reformat alone**

```bash
git add -A
git commit -m "style: apply prettier and eslint across resources"
```

- [ ] **Step 9: Add the lint workflow**

Create `.github/workflows/lint.yml`:

```yaml
name: linter

on:
  push:
    branches: [main]
  pull_request:
    branches: [main]

jobs:
  quality:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v7

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.5'

      - name: Setup Node.js
        uses: actions/setup-node@v6
        with:
          node-version: '22'
          cache: 'npm'

      - name: Install dependencies
        run: |
          composer install -q --no-ansi --no-interaction --no-scripts --no-progress --prefer-dist
          npm ci

      - name: Run Pint
        run: composer test:lint

      - name: Check formatting
        run: npm run format:check

      - name: Lint
        run: npm run lint
```

Note this uses `format:check` and `test:lint`, not TryPost's write-mode `format`/`lint` — a CI job that rewrites files it never commits reports success on a dirty tree.

- [ ] **Step 10: Commit**

```bash
git add .github/workflows/lint.yml
git commit -m "chore: add the lint workflow"
```

---

### Task 5: The Docker image

**Files:**
- Create: `docker/Dockerfile`, `docker/entrypoint.sh`, `docker/nginx.conf`, `docker/supervisord.dev.conf`, `docker/supervisord.prod.conf`, `docker/php.dev.ini`, `docker/php.prod.ini`, `docker/.env.docker.example`, `.dockerignore`

- [ ] **Step 1: Copy the reference set**

```bash
mkdir -p docker
cp ~/Herd/trypost/docker/Dockerfile docker/
cp ~/Herd/trypost/docker/entrypoint.sh docker/
cp ~/Herd/trypost/docker/nginx.conf docker/
cp ~/Herd/trypost/docker/supervisord.dev.conf docker/
cp ~/Herd/trypost/docker/supervisord.prod.conf docker/
cp ~/Herd/trypost/docker/php.dev.ini docker/
cp ~/Herd/trypost/docker/php.prod.ini docker/
cp ~/Herd/trypost/docker/.env.docker.example docker/
cp ~/Herd/trypost/.dockerignore ./.dockerignore
chmod +x docker/entrypoint.sh
```

- [ ] **Step 2: Rebrand and re-version the Dockerfile**

In `docker/Dockerfile`:
- `ARG PHP_VERSION=8.4` becomes `ARG PHP_VERSION=8.5`.
- Every `TRYPOST_TARGET` becomes `LUA_TARGET`; every `99-trypost.ini` becomes `99-lua.ini`; the header comment's `trypost:dev` / `trypost:prod` become `lua:dev` / `lua:prod`.
- The `asset-build` build args become Lua's:

```dockerfile
ARG VITE_APP_NAME=Lua
ARG VITE_REVERB_APP_KEY=
ARG VITE_REVERB_HOST=localhost
ARG VITE_REVERB_PORT=8080
ARG VITE_REVERB_SCHEME=http
ARG VITE_POSTHOG_ENABLED=false
ARG VITE_POSTHOG_API_KEY=
ARG VITE_POSTHOG_HOST=https://us.i.posthog.com
```

with the matching `ENV` lines below them.
- `npm run build && npm run build:ssr` becomes **`npm run build`** alone. Lua's `build` script already runs both passes (`vite build && vite build --ssr`); there is no separate `build:ssr` script and calling one fails the build.

- [ ] **Step 3: Add the SSR program to both supervisord configs**

This is the adaptation that is not in the reference. `CLAUDE.md` states SSR runs in production and that a crashed SSR process is silent — pages fall back to client rendering and still look correct. Without this program the image ships with SSR permanently dead and nothing reports it.

Append to **both** `docker/supervisord.dev.conf` and `docker/supervisord.prod.conf`:

```ini
[program:inertia-ssr]
command=php /var/www/html/artisan inertia:start-ssr
directory=/var/www/html
autostart=true
autorestart=true
startsecs=5
priority=35
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
```

- [ ] **Step 4: Rename the ini files' comment header**

In `docker/php.prod.ini` and `docker/php.dev.ini`, change the first line from `; TryPost — ...` to `; Lua — ...`.

- [ ] **Step 5: Teach the entrypoint about MySQL**

In `docker/entrypoint.sh`, replace every `TRYPOST_` prefix with `LUA_`, and replace the Postgres-only wait block (step 6 in that file) with one that dispatches on the driver:

```sh
# 6) Wait for the database to be reachable. Lua supports Postgres and MySQL,
#    so the probe follows DB_CONNECTION rather than assuming one.
DB_CONN_VALUE="${DB_CONNECTION:-pgsql}"
DB_HOST_VALUE="${DB_HOST:-pgsql}"
DB_USER_VALUE="${DB_USERNAME:-postgres}"
DB_NAME_VALUE="${DB_DATABASE:-lua}"

if [ "${DB_CONN_VALUE}" = "mysql" ] || [ "${DB_CONN_VALUE}" = "mariadb" ]; then
    DB_PORT_VALUE="${DB_PORT:-3306}"
else
    DB_PORT_VALUE="${DB_PORT:-5432}"
fi

echo "[entrypoint] waiting for ${DB_CONN_VALUE} at ${DB_HOST_VALUE}:${DB_PORT_VALUE}"
WAIT_ATTEMPTS=0
while true; do
    if [ "${DB_CONN_VALUE}" = "mysql" ] || [ "${DB_CONN_VALUE}" = "mariadb" ]; then
        mysqladmin ping -h "${DB_HOST_VALUE}" -P "${DB_PORT_VALUE}" --silent >/dev/null 2>&1 && break
    else
        pg_isready -h "${DB_HOST_VALUE}" -p "${DB_PORT_VALUE}" -U "${DB_USER_VALUE}" -d "${DB_NAME_VALUE}" >/dev/null 2>&1 && break
    fi
    WAIT_ATTEMPTS=$((WAIT_ATTEMPTS + 1))
    if [ "${WAIT_ATTEMPTS}" -gt 60 ]; then
        echo "[entrypoint] database not reachable after 60s — continuing anyway"
        break
    fi
    sleep 1
done
```

Also change `php artisan wayfinder:generate --with-form` to `php artisan wayfinder:generate` unless `php artisan wayfinder:generate --help` shows Lua's version supports `--with-form`; check before keeping the flag.

- [ ] **Step 6: Warn about MySQL timezone tables in the env template**

Add to `docker/.env.docker.example`, near the database block:

```
# MySQL only: analytics timeseries use CONVERT_TZ with a named zone, which
# returns NULL unless the server's timezone tables are loaded. Without this,
# every chart reads as zero with no error. Load them once with:
#   mysql_tzinfo_to_sql /usr/share/zoneinfo | mysql -u root -p mysql
```

Also replace TryPost's app name, URLs and any TryPost-specific service keys with Lua's.

- [ ] **Step 7: Build both targets**

```bash
docker build --target production -t lua:prod -f docker/Dockerfile .
docker build --target dev -t lua:dev -f docker/Dockerfile .
```
Expected: both succeed. A failure in `asset-build` most likely means the Vite/Wayfinder step is missing PHP or an env var — read the stage comment in the Dockerfile.

- [ ] **Step 8: Commit**

```bash
git add docker .dockerignore
git commit -m "feat: add the docker image, with the ssr process supervised"
```

---

### Task 6: Compose files, replacing Sail's

The root `docker-compose.yml` is Sail's and is broken: it builds `vendor/laravel/sail/runtimes/8.2` while `composer.json` requires PHP `^8.5`, so `composer install` inside it cannot succeed.

**Files:**
- Create: `compose.yaml`, `compose.prod.yaml`, `compose.override.yaml.example`
- Delete: `docker-compose.yml`

- [ ] **Step 1: Copy and adapt**

```bash
cp ~/Herd/trypost/compose.yaml ./compose.yaml
cp ~/Herd/trypost/compose.prod.yaml ./compose.prod.yaml
cp ~/Herd/trypost/compose.override.yaml.example ./compose.override.yaml.example
```

Read each one and replace: service/container names (`trypost` → `lua`), the image name, database names (`trypost` → `lua`), and every `TRYPOST_` env prefix with `LUA_`.

- [ ] **Step 2: Remove Sail's compose file**

```bash
git rm docker-compose.yml
```

- [ ] **Step 3: Boot the stack and verify the app answers**

```bash
docker compose up -d
sleep 30
curl -fsS http://localhost/up && echo "OK"
```
Expected: `OK`.

- [ ] **Step 4: Verify SSR is actually running, not silently dead**

```bash
docker compose exec app sh -c "ps aux | grep -c '[i]nertia:start-ssr'"
```
Expected: `1` or more.

Then confirm the server is really rendering markup rather than falling back:

```bash
curl -fsS http://localhost/ | grep -c 'data-page' && \
curl -fsS http://localhost/ | grep -qi '<div id="app"></div>' \
  && echo "EMPTY SHELL — SSR is falling back" || echo "server-rendered"
```
Expected: `server-rendered`.

- [ ] **Step 5: Tear down and commit**

```bash
docker compose down -v
git add compose.yaml compose.prod.yaml compose.override.yaml.example
git commit -m "chore: replace sail's compose file with the project's own"
```

---

### Task 7: Publish the image on a release tag

**Files:**
- Create: `.github/workflows/release-docker.yml`

- [ ] **Step 1: Copy the reference**

```bash
cp ~/Herd/trypost/.github/workflows/release-docker.yml .github/workflows/release-docker.yml
```

- [ ] **Step 2: Adapt the build args**

The only Lua-specific change is the `build-args` block in the "Build and push by digest" step. Replace TryPost's with:

```yaml
          build-args: |
            VITE_APP_NAME=Lua
```

Everything else — the amd64/arm64 native-runner matrix, push-by-digest, the manifest merge, the semver tag set plus `latest` — carries over unchanged.

- [ ] **Step 3: Validate the workflow parses**

```bash
for f in .github/workflows/*.yml; do
  python3 -c "import sys,yaml; yaml.safe_load(open('$f')); print('$f ok')" 2>/dev/null \
    || ruby -ryaml -e "YAML.load_file('$f'); puts '$f ok'"
done
```
Expected: every file reports `ok`. If neither Python's `yaml` nor Ruby is available, use `gh workflow view` after pushing instead.

- [ ] **Step 4: Commit**

```bash
git add .github/workflows/release-docker.yml
git commit -m "feat: publish a multi-arch image to ghcr on every version tag"
```

---

### Task 8: The `/release` command

**Files:**
- Create: `.claude/commands/release.md`, `.claude/release-assets/render-thumbnail.mjs`, `.claude/release-assets/thumbnail.template.html`, `.claude/release-assets/logo.png`

- [ ] **Step 1: Copy the reference set**

```bash
mkdir -p .claude/release-assets
cp ~/Herd/trypost/.claude/commands/release.md .claude/commands/release.md
cp ~/Herd/trypost/.claude/release-assets/render-thumbnail.mjs .claude/release-assets/
cp ~/Herd/trypost/.claude/release-assets/thumbnail.template.html .claude/release-assets/
```

- [ ] **Step 2: Rewrite the command for Lua**

In `.claude/commands/release.md`, keep the mechanics exactly — sequential versioning with rollover at 9, GitHub's native `generate-notes` changelog used unmodified, the humanizer pass, the confirm-before-push gate, the artifacts PR — and change only what is TryPost-specific:

- Every `TryPost` becomes `Lua`; `TryPost Product Team` becomes `Lua Product Team`.
- The signature `Cheers,\nPaulo from TryPost.it` becomes `Cheers,\nPaulo from lua.sh`.
- The thumbnail description paragraph is rewritten for Lua's brand (see Step 3).
- The product claims in the email guidance must be true of Lua. `.claude/blog-context.md` holds the list of what Lua does **not** do — the release email is bound by it exactly as the blog is.

- [ ] **Step 3: Rebrand the thumbnail template**

`thumbnail.template.html` is TryPost's cream/lavender wash with an Instrument Serif headline. Rebuild it on Lua's tokens, which are documented in `CLAUDE.md` under "Design system":

- Accent: Heat `#FA5D19`. As **text** it must use `#be3c04` — Heat on white is 3.16:1 and fails contrast.
- Display face: **Schibsted Grotesk 700**, tracking `-0.032em`.
- Foreground is **pure black `#000000`**, not near-black — the wordmark is drawn in `fill="black"` and a near-black sits visibly apart from it.
- Use the wordmark from `public/` (`full-black.svg`) rather than copying TryPost's `logo.png`.

Find the wordmark first:

```bash
ls public/*.svg public/images/*.svg 2>/dev/null | head
```

- [ ] **Step 4: Render a test thumbnail**

```bash
mkdir -p /tmp/lua-release-test
node .claude/release-assets/render-thumbnail.mjs \
  --version "v0.0.0" \
  --headline "Every link, measured" \
  --underline "measured" \
  --themes "Analytics,QR codes,API" \
  --out /tmp/lua-release-test/thumbnail.png
```
Expected: a 1200×630 PNG is written. Open it and confirm the wordmark, the accent and the display face are Lua's, not TryPost's.

```bash
file /tmp/lua-release-test/thumbnail.png
```
Expected: `PNG image data, 1200 x 630`.

- [ ] **Step 5: Verify no TryPost references survive**

```bash
grep -rni "trypost" .claude/ docker/ compose*.yaml .github/ .dockerignore || echo "clean"
```
Expected: `clean`.

- [ ] **Step 6: Commit**

```bash
git add .claude
git commit -m "feat: add the release command and its brand assets"
```

---

### Task 9: Full verification and the PR

- [ ] **Step 1: Both engines, full suite**

```bash
php artisan test --compact
DB_CONNECTION=mysql DB_HOST=127.0.0.1 DB_PORT=3306 DB_USERNAME=root DB_PASSWORD= DB_DATABASE=lua_test \
  php artisan test --compact
```
Expected: 535 passed on each.

- [ ] **Step 2: Browser suite**

```bash
npm run build
php artisan test tests/Browser --compact
```
Expected: 49 passed.

- [ ] **Step 3: Typecheck, lint, format**

```bash
npx vue-tsc --noEmit
npm run format:check
npm run lint
vendor/bin/pint --dirty --format agent
```
Expected: all clean.

- [ ] **Step 4: Confirm husky really is gone**

```bash
git config --get core.hooksPath && echo "STILL SET" || echo "unset"
ls .husky 2>/dev/null && echo "STILL THERE" || echo "gone"
```
Expected: `unset` and `gone`.

- [ ] **Step 5: Push and open the PR**

Do this only after every step above is green, and only once the user has confirmed.

```bash
git push -u origin chore/cicd-release-docker
```

Then open the PR against `main`, describing: the red-CI root cause and its fix, the MySQL timezone finding, the SSR supervisord addition, the Sail compose replacement, and the husky/commitlint removal.

- [ ] **Step 6: Report what CI cannot do for itself**

Tell the user explicitly: the `backend` job must be marked as a **required status check** on `main` in Settings → Branches. Nothing in this PR can set that, and until it is set, merging over red stays possible — which is the problem this work started from.

---

## Notes for the executor

- **Do not "fix" a MySQL test by loosening its assertion.** If a test fails on MySQL and not PostgreSQL, the app is doing something non-portable. `CLAUDE.md` lists the traps that actually occur here.
- **`git rm` the files being replaced**, do not just create the new ones — a leftover `backend-tests.yml` would keep running and keep failing.
- The reference repo is a working system. When something ported does not behave, read the neighbouring file in `~/Herd/trypost` before inventing a fix.
