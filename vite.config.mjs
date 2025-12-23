import { createViteConfig } from "vite-config-factory";

const entries = {
    'css/modularity-latest-events': './source/sass/modularity-latest-events.scss',
};

export default createViteConfig(entries, {
    outDir: "assets/dist",
    manifestFile: "manifest.json",
});
