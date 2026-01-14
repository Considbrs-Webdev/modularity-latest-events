<?php

declare(strict_types=1);

namespace ModularityLatestEvents\Module;

use ModularityLatestEvents\Helper\CacheBust;

/**
 * Class LatestEvents
 * @package ModularityLatestEvents\Module
 */
class LatestEvents extends \Modularity\Module
{
    public $slug = 'latest-events';
    public $supports = [];

    public function init(): void
    {
        $this->nameSingular = __('Latest Events', 'modularity-latest-events');
        $this->namePlural = __('Latest Events', 'modularity-latest-events');
        $this->description = __('A latest-events module.', 'modularity-latest-events');
    }

    /**
     * Data array
     * @return array $data
     */
    public function data(): array
    {
        $data = [];

        // Append field config
        $data = array_merge($data, (array) \Modularity\Helper\FormatObject::camelCase(
            $this->getFields(),
        ));

        // Get icon fields
        $data['dateIcon'] = get_field('date_icon', $this->ID) ?: 'calendar_today';
        $data['locationIcon'] = get_field('location_icon', $this->ID) ?: 'location_on';
        $data['categoryIcon'] = get_field('category_icon', $this->ID) ?: 'category';
        $data['iconColor'] = get_field('icon_color', $this->ID) ?: '#666666';

        return $data;
    }

    /**
     * Blade Template
     * @return string
     */
    public function template(): string
    {
        return 'latest-events.blade.php';
    }

    /**
     * Style - Register & adding css
     * @return void
     */
    public function style(): void
    {
        $this->wpEnqueue?->add('css/modularity-latest-events.css', [], '1.0.0');
    }

    /**
     * Script - Register & adding js
     * @return void
     */
    public function script(): void
    {
        $scriptFile = CacheBust::name('js/modularity-latest-events.js');

        if ($scriptFile) {
            wp_enqueue_script(
                'modularity-latest-events',
                MODULARITYLATESTEVENTS_URL . '/assets/dist/' . $scriptFile,
                [],
                null,
                true
            );
        }
    }

    /**
     * Available "magic" methods for modules:
     * init()            What to do on initialization
     * data()            Use to send data to view (return array)
     * style()           Enqueue style only when module is used on page
     * script            Enqueue script only when module is used on page
     * adminEnqueue()    Enqueue scripts for the module edit/add page in admin
     * template()        Return the view template (blade) the module should use when displayed
     */
}
