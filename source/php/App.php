<?php

declare(strict_types=1);

namespace ModularityLatestEvents;

use ModularityLatestEvents\Api\EventProxy;
use ModularityLatestEvents\Helper\CacheBust;

/**
 * Class App
 *
 * Main application bootstrap class.
 * Initialize your plugin components here.
 *
 * @package ModularityLatestEvents
 */
class App
{
    public function __construct()
    {
        add_action('init', [$this, 'registerModule']);
        add_action('enqueue_block_assets', [$this, 'enqueueEditorStyles']);

        new EventProxy();
    }

    /**
     * Enqueue the module stylesheet inside the block editor iframe.
     *
     * Does not load the front-end events script.
     *
     * @return void
     */
    public function enqueueEditorStyles(): void
    {
        if (!is_admin()) {
            return;
        }

        $url = $this->stylesheetUrl();
        if ($url === '') {
            return;
        }

        wp_enqueue_style('modularity-latest-events', $url, [], null);
    }

    /**
     * Built stylesheet URL, or an empty string when the Vite manifest has no entry.
     */
    private function stylesheetUrl(): string
    {
        $styleFile = CacheBust::name('css/modularity-latest-events.css');
        if (!$styleFile) {
            return '';
        }

        return MODULARITYLATESTEVENTS_URL . '/assets/dist/' . $styleFile;
    }

    /**
     * Register the module with Modularity
     * 
     * @return void
     */
    public function registerModule(): void
    {
        if (function_exists('modularity_register_module')) {
            modularity_register_module(
                MODULARITYLATESTEVENTS_MODULE_PATH,
                'LatestEvents',
            );
        }
    }
}
