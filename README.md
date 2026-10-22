# Modularity Latest Events

WordPress plugin that registers a Modularity module for the latest Visit Piteå events. The public page loads the list through an admin-ajax proxy. The block editor shows a short preview.

## Requirements

- PHP and Composer
- Node.js and npm
- A WordPress site with Municipio, Modularity, ACF, and AcfExportManager. `composer.json` suggests `helsingborg-stad/municipio` ^6.

This working copy lives in the pitea-se site at `https://pitea-se.ddev.site`.

## Getting Started

1. Clone this repository into `wp-content/plugins/`
2. Run `composer install` to generate the PHP autoloader (there are no Composer packages)
3. Run `npm install` to install Node dependencies
4. Copy `.env.example` to `.env` and set `VISITPITEA_API_TOKEN`, or set the WordPress options `modularity_latest_events_api_url` and `modularity_latest_events_api_token`
5. Run `npm run build` to build assets
6. Activate **Modularity Latest Events** in WordPress

## Development

- `npm run dev` — Vite dev server
- `npm run watch` — rebuild on change
- `npm run build` — production build into `assets/dist/` (gitignored)
- `npm run build:dev` — development Vite build
- `composer test` — PHPUnit. The suite stubs WordPress and does not read `.env`.

`npm run lint` and `npm run format` are not usable yet: they call `npx biome` without a Biome config or the `@biomejs/biome` dependency.

## Release

Integration branch is `dev`. Release branch is `main`. The version is `1.0.0` in the plugin header and `package.json`. Tags use a `v` prefix, so the first tag is `v1.0.0`. `GitVersion.yml` calculates the next number from Conventional Commits on `main`. See `AGENTS.md` before cutting a release.

`php build.php` dumps the autoloader, runs `npm ci`, and builds assets. Do not pass `--cleanup` in a working tree.

## Structure

```
├── modularity-latest-events.php   # Plugin bootstrap
├── source/
│   ├── php/
│   │   ├── App.php                # Hooks and editor stylesheet
│   │   ├── Api/EventProxy.php     # Visit Piteå API proxy
│   │   ├── AcfFields/             # ACF field export
│   │   ├── Helper/CacheBust.php   # Vite manifest lookup
│   │   └── Module/                # LatestEvents module, icon, Blade views
│   ├── js/simpleview-events.ts    # Public event list
│   └── sass/                      # Module SCSS
├── tests/                         # PHPUnit suite
├── languages/                     # Translations (text domain modularity-latest-events)
├── assets/dist/                   # Compiled assets (generated, not committed)
└── build.php                      # Deploy build
```

## AI assistants

Instructions for AI tools are in `AGENTS.md`.

## License

MIT
