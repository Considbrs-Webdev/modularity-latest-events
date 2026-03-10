<?php

if (function_exists('acf_add_local_field_group')) {

    acf_add_local_field_group(array(
        'key' => 'group_latest-events_module',
        'title' => __('LatestEvents Module', 'modularity-latest-events'),
        'fields' => array(
            0 => array(
                'key' => 'field_latest-events_date_icon',
                'label' => __('Date Icon', 'modularity-latest-events'),
                'name' => 'date_icon',
                'aria-label' => '',
                'type' => 'icon',
                'instructions' => __('Select icon for event date', 'modularity-latest-events'),
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => 'calendar_today',
            ),
            1 => array(
                'key' => 'field_latest-events_location_icon',
                'label' => __('Location Icon', 'modularity-latest-events'),
                'name' => 'location_icon',
                'aria-label' => '',
                'type' => 'icon',
                'instructions' => __('Select icon for event location', 'modularity-latest-events'),
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => 'location_on',
            ),
            2 => array(
                'key' => 'field_latest-events_category_icon',
                'label' => __('Category Icon', 'modularity-latest-events'),
                'name' => 'category_icon',
                'aria-label' => '',
                'type' => 'icon',
                'instructions' => __('Select icon for event category', 'modularity-latest-events'),
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => 'category',
            ),
            3 => array(
                'key' => 'field_latest-events_icon_color',
                'label' => __('Icon Color', 'modularity-latest-events'),
                'name' => 'icon_color',
                'aria-label' => '',
                'type' => 'color_picker',
                'instructions' => __('Select color for the icons', 'modularity-latest-events'),
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '#666666',
            ),
            4 => array(
                'key' => 'field_latest-events_calendar_url',
                'label' => __('Events Calendar URL', 'modularity-latest-events'),
                'name' => 'events_calendar_url',
                'aria-label' => '',
                'type' => 'url',
                'instructions' => __('URL for the "Till evenemangskalendern" link. Leave empty to hide the link.', 'modularity-latest-events'),
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
            ),
        ),
        'location' => array(
            0 => array(
                0 => array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'mod-latest-events',
                ),
            ),
            1 => array(
                0 => array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/latest-events',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'show_in_rest' => 0,
    ));

}

