<?php

declare(strict_types=1);

namespace ModularityLatestEvents\Module;

use ModularityLatestEvents\Api\EventProxy;
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

        $data['dateIcon'] = get_field('date_icon', $this->ID) ?: 'calendar_today';
        $data['iconColor'] = get_field('icon_color', $this->ID) ?: '#666666';
        $data['eventsCalendarUrl'] = (string) (get_field('events_calendar_url', $this->ID) ?: '');

        if ($this->isBlockEditorPreview()) {
            $data['isEditorPreview'] = true;
            $iconColor = $data['iconColor'] ?? '#666666';
            $data['editorIconColor'] = $this->sanitizeIconColor(is_string($iconColor) ? $iconColor : '#666666');
            $preview = EventProxy::instance()->getEditorPreviewEvents();

            if (is_wp_error($preview)) {
                $data['editorPreviewState'] = 'unavailable';
                $data['editorEvents'] = [];
            } elseif ($preview === []) {
                $data['editorPreviewState'] = 'empty';
                $data['editorEvents'] = [];
            } else {
                $data['editorPreviewState'] = 'ready';
                $data['editorEvents'] = $preview;
            }
        }

        return $data;
    }

    /**
     * True while ACF is rendering the block into the editor canvas.
     */
    private function isBlockEditorPreview(): bool
    {
        return function_exists('acf_get_data') && (bool) acf_get_data('acf_doing_block_preview');
    }

    /**
     * Hex colour safe to print in a style attribute.
     */
    private function sanitizeIconColor(string $color): string
    {
        if (preg_match('/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $color) === 1) {
            return $color;
        }

        return '#666666';
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

            wp_localize_script('modularity-latest-events', 'modLatestEvents', [
                'ajaxUrl' => admin_url('admin-ajax.php'),
            ]);
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
