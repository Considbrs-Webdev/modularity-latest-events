<?php

declare(strict_types=1);

namespace ModularityLatestEvents;

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

        new EventProxy();
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
