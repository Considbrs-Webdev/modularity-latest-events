<?php

namespace ModularityLatestEvents;

use ModularityLatestEvents\Helper\CacheBust;
use ModularityLatestEvents\Api\EventProxy;

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
        add_action('wp_enqueue_scripts', [$this, 'enqueueStyles']);

        new EventProxy();
    }

    /**
     * Enqueue styles
     * 
     * @return void
     */
    public function enqueueStyles(): void
    {
        $styleFile = CacheBust::name('css/modularity-latest-events.css');

        if ($styleFile) {
            wp_enqueue_style(
                'modularity-latest-events',
                MODULARITYLATESTEVENTS_URL . '/assets/dist/' . $styleFile,
                [],
                null
            );
        }
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
