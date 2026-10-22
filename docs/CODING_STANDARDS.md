# Coding standards

Applies to new and changed code. Deviations in older code are not by themselves a reason to
rewrite it. Each rule is marked as enforced by tooling or as an observed pattern. Observed
patterns are proposals. The responsible developers decide which rules stay.

Nothing in this file is enforced by CI. There is no CI workflow.

## Language and tooling

- PHP uses 4-space indentation and the PSR-4 prefix `ModularityLatestEvents\` mapped to `source/php/`. Observed pattern.
- `declare(strict_types=1);` is present in `App`, `EventProxy`, `LatestEvents`, and the PHPUnit files. It is absent from `CacheBust` and `modularity-latest-events.php`. Observed pattern. Worth extending to every new PHP file. Not worth a sweep of old files until the team decides.
- TypeScript is `strict` in `tsconfig.json` (`source/js/**/*`). Enforced by tooling when `tsc` is run. Nothing in `package.json` runs `tsc`. The production check that does run is `npm run build` (Vite 7). Enforced by tooling for the build; the strict compiler is configuration only.
- `.vscode/settings.json` sets Biome as the editor formatter for non-PHP files and format-on-save. `npm run lint` and `npm run format` call `npx biome` without `@biomejs/biome` in `devDependencies` and without a `biome.json`. Those scripts do not lint this project. Observed intent, not enforced. Worth fixing before anyone treats them as a gate.
- The same settings file formats PHP with `vendor/bin/mago`. Mago is not a Composer dependency, and `vendor/bin` is empty after `composer install`. Observed intent, not enforced. Worth deciding whether Mago should be added, or the setting removed.
- Translations use the text domain `modularity-latest-events`. User-facing strings in the Blade views are Swedish. Observed pattern.

## Naming and structure

- Module slug `latest-events`, Modularity view key `mod-latest-events`, CSS block `.mod-latest-events`, card block `.c-event-card`. Observed pattern.
- One module class in `source/php/Module/LatestEvents.php`, extending `\Modularity\Module`, with `init`, `data`, `template`, `style`, and `script`. Observed pattern.
- ACF fields are exported under `source/php/AcfFields/` and imported on `acf/init` from the main plugin file. Edit the field group through that export path. Observed pattern.
- Front-end events are rendered in `source/js/simpleview-events.ts` from `data-*` attributes. The editor preview is server-rendered Blade. Observed pattern.
- npm package name is `@considbrs/modularity-latest-events`. Repository URLs point at `Considbrs-Webdev/modularity-latest-events`. Observed pattern. The Municipio package in `composer.json` stays `helsingborg-stad/municipio` because that is the upstream package name.

## Error handling

- API and ajax failures return `wp_send_json_error` with a fixed message, or `WP_Error` for the editor preview. The template shows a placeholder instead of an empty editor list. Observed pattern.
- Missing Vite manifest entries skip enqueue rather than printing a broken URL (`CacheBust::name` returns `false`). Observed pattern.

## Testing

- PHPUnit 11 runs with `composer test`. Config is `phpunit.xml.dist`. Tests live in `tests/` and end in `Test.php`. One behavior per test. Enforced by tooling when the suite is run. Nothing runs it in CI.
- WordPress is stubbed. Tests use synthetic API data and must not read `.env`. Observed pattern.
- `CacheBust` is covered through `CacheBust::name`. The editor event list is covered through `EventProxy::getEditorPreviewEvents`. The ajax handler, Blade templates, and the TypeScript renderer have no tests.

## Security

- Read the API base URL and bearer token from the WordPress options, then from `.env`. Do not hard-code them. Observed pattern. `.env` is gitignored; `.env.example` holds placeholders only.
- Clamp `per_page` (the handler uses `min(abs(intval(...)), 12)`). Observed pattern.
- Escape text before HTML injection. The script decodes entities and then escapes. Blade `{{ }}` escapes, and the calendar link uses `esc_url`. Editor icon colours are restricted to `#rgb` / `#rrggbb` before they are printed in a style attribute. Observed pattern.
- The events action is public (`wp_ajax_nopriv_latest_events`). Keep responses to public event fields. Observed pattern.

## Dependencies

- PHP runtime libraries used by this plugin (`AcfExportManager`, Modularity, ACF, WordPress) are provided by the host, not by `composer.json`. Do not vendor them here. Observed pattern.
- Front-end build dependencies are the `devDependencies` in `package.json`: TypeScript, Vite, `vite-config-factory`, `sass-embedded`. Enforced by tooling via the lockfile when `npm ci` / `npm install` is used.
- PHPUnit is a Composer dev dependency. Enforced by tooling via `composer.lock`.

## Git and reviews

- Long-lived branches are `dev` (integration) and `main` (release). Feature branches use a `feat/` prefix. Observed from history and confirmed for this setup.
- `GitVersion.yml` expects Conventional Commits and a `v` tag prefix (`v1.0.0`). The declared version is `1.0.0`. Recent commits on `dev` do not use that prefix. Observed conflict. Worth picking one rule and following it for new commits. Do not rewrite history to match.
- Pull requests are opened on GitHub against the integration branch. Observed pattern.
