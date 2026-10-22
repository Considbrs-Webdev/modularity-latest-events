# AGENTS.md

Instructions for AI tools working in this repository. Read this first.

## Project

WordPress plugin that registers the Modularity module `latest-events`. The public page loads four upcoming events from the Visit Piteå API through an admin-ajax proxy. The block editor shows a short cached preview. PHP (PSR-4 namespace `ModularityLatestEvents`), Blade views, TypeScript, and SCSS built with Vite. It runs inside a Municipio site; `composer.json` suggests `helsingborg-stad/municipio` ^6. TODO: confirm which Municipio major versions this plugin supports. This working copy is the plugin directory inside the pitea-se site (`https://pitea-se.ddev.site`).

## Setup and commands

Copy `.env.example` to `.env` and set `VISITPITEA_API_TOKEN`, or set the WordPress options `modularity_latest_events_api_url` and `modularity_latest_events_api_token`. Options win when both are set. Do not open `.env`.

| Task | Command |
| --- | --- |
| Install | `composer install` then `npm install` |
| Test | `composer test` |
| Assets, dev | `npm run dev` or `npm run watch` |
| Build | `npm run build` (`npm run build:dev` for a development Vite build) |
| Package for deploy | `php build.php` |

`composer install` installs PHPUnit and generates the PSR-4 autoloader. Runtime libraries are not Composer packages. WordPress, Modularity, ACF, and `AcfExportManager` come from the host site.

There is no CI workflow and no PHP linter. `npm run lint` and `npm run format` call `npx biome`, which is not a devDependency and currently resolves to the unrelated package `biome@0.3.3`. There is no `biome.json`. `.vscode/settings.json` formats PHP with `vendor/bin/mago`, which is not installed. Do not report those commands as passed.

`assets/dist/` is generated and gitignored. Rebuild it when `source/js/` or `source/sass/` changes.

## Structure

- `modularity-latest-events.php` — plugin header, constants, textdomain, ACF import/export, Modularity view path, boots `App`.
- `source/php/App.php` — registers the module and enqueues editor CSS.
- `source/php/Api/EventProxy.php` — Visit Piteå API proxy (`wp_ajax_latest_events` and `wp_ajax_nopriv_latest_events`) and the editor preview.
- `source/php/Module/LatestEvents.php` — Modularity module class.
- `source/php/Module/views/` — Blade templates. Public list is filled by `source/js/simpleview-events.ts`.
- `source/php/AcfFields/` — ACF JSON and PHP export for field group `latest-events-module`.
- `source/php/Helper/CacheBust.php` — hashed filenames from the Vite manifest.
- `tests/` — PHPUnit suite. WordPress functions are stubbed in `tests/wp-stubs.php`. The bootstrap points `MODULARITYLATESTEVENTS_PATH` at a temp directory so tests do not read `.env`.
- `source/sass/modularity-latest-events.scss` — module styles.
- `languages/` — text domain `modularity-latest-events`. Swedish is `sv_SE`.
- `build.php` — CLI packaging script. `--cleanup` deletes source, docs, and `.git` unless `--release` is also passed; `--release` still deletes the other dev files. Do not run `--cleanup` in a working tree.
- `vendor/`, `node_modules/`, and `assets/` are generated. Do not edit them.

## Conventions

- Follow `docs/CODING_STANDARDS.md` for new and changed code.
- Do not refactor unrelated code. Deviations in older code are not a reason to rewrite it.
- Comments explain why, briefly. Never refer to documents from code comments; restate the relevant information in the comment instead.
- Run `composer test` before saying PHP work is done. Verify asset changes with `npm run build` as well. The suite does not boot WordPress.

## Release

The shared release skill reads this section first.

- Development branch is `dev`. Feature branches look like `feat/<name>` and merge through GitHub pull requests. Release branch is `main`.
- `GitVersion.yml` is MainLine with `tag-prefix: '[vV]'`. Only `main` and `master` are release branches. Tags look like `v1.0.0`. The declared version is `1.0.0`. It expects Conventional Commits (`feat` is minor, `fix` and the other listed types are patch, a `BREAKING CHANGE` footer is major). History on `dev` is mostly sentence-style messages, so the configured rule and the practice differ. TODO: decide whether new commits must use Conventional Commits.
- Move these together: `Version:` in `modularity-latest-events.php`, `version` in `package.json`, and the root `version` in `package-lock.json`. `composer.json` has no version. `LatestEvents::style()` also passes the literal `1.0.0` into the style enqueue. Do not bump the version while it is still `1.0.0` unless cutting a release.
- There is no `CHANGELOG.md` and there are no git tags. Ask before creating a changelog. Ask before `gh release create`; no GitHub releases exist yet. The first tag, when cut, is `v1.0.0`.
- Release checks are `composer test` and `npm run build`.

## Where to find more

Field definitions live in `source/php/AcfFields/json/latest-events-module.json`. There are no ADRs or other architecture docs in this repo.

## Security and data

- Secrets come from `.env` (`VISITPITEA_API_URL`, `VISITPITEA_API_TOKEN`) or the WordPress options named above. Never read, print, or commit their values, and never put them in instructions, code, or logs.
- Event payloads are public tourism data. Use synthetic or anonymized data in examples and fixtures where possible.
- The ajax action is available to logged-out visitors. Keep `per_page` bounded, and escape event text before it is written into the page.

## Working with AI

- The developer is responsible for understanding, reviewing, and verifying everything AI produces.
- If a guard or permission blocks an action, do not work around it. Report it and let the developer decide.
- If these instructions cause unnecessary steps or unwanted behavior, tell the developer so this file can be improved.
- Skills are copied in `.agents/skills/` and linked from `.cursor/skills`. `AGENTS.md` is the source of truth. Toolkit pin: `.agents/skills/.toolkit-version`.
