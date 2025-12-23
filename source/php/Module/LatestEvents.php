<?php

declare(strict_types=1);

namespace ModularityLatestEvents\Module;

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
        $this->nameSingular = __('LatestEvents', 'modularity-latest-events');
        $this->namePlural = __('LatestEvents', 'modularity-latest-events');
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
     * Available "magic" methods for modules:
     * init()            What to do on initialization
     * data()            Use to send data to view (return array)
     * style()           Enqueue style only when module is used on page
     * script            Enqueue script only when module is used on page
     * adminEnqueue()    Enqueue scripts for the module edit/add page in admin
     * template()        Return the view template (blade) the module should use when displayed
     */
}

