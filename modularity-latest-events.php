<?php

/**
 * Plugin Name:       Modularity LatestEvents
 * Plugin URI:        https://github.com/helsingborg-stad/modularity-latest-events
 * Description:       A latest-events for creating Modularity modules.
 * Version: 1.0.0
 * Author:            Starter
 * Author URI:        https://github.com/helsingborg-stad
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       modularity-latest-events
 * Domain Path:       /languages
 */

// Protect against direct file access
if (! defined('WPINC')) {
    die;
}

define('MODULARITYLATESTEVENTS_PATH', plugin_dir_path(__FILE__));
define('MODULARITYLATESTEVENTS_URL', plugins_url('', __FILE__));
define('MODULARITYLATESTEVENTS_MODULE_VIEW_PATH', plugin_dir_path(__FILE__) . 'source/php/Module/views');
define('MODULARITYLATESTEVENTS_MODULE_PATH', MODULARITYLATESTEVENTS_PATH . 'source/php/Module/');

// Load text domain
add_action('init', function () {
    load_plugin_textdomain('modularity-latest-events', false, plugin_basename(dirname(__FILE__)) . '/languages');
});

// Autoload from plugin
if (file_exists(MODULARITYLATESTEVENTS_PATH . 'vendor/autoload.php')) {
    require_once MODULARITYLATESTEVENTS_PATH . 'vendor/autoload.php';
}

// ACF auto import and export
add_action('acf/init', function () {
    $acfExportManager = new \AcfExportManager\AcfExportManager();
    $acfExportManager->setTextdomain('modularity-latest-events');
    $acfExportManager->setExportFolder(MODULARITYLATESTEVENTS_PATH . 'source/php/AcfFields/');
    $acfExportManager->autoExport(array(
        'latest-events-module' => 'group_latest-events_module',
    ));
    $acfExportManager->import();
});

// Modularity 3.0 ready - ViewPath for Component library
add_filter('/Modularity/externalViewPath', function ($arr) {
    $arr['mod-latest-events'] = MODULARITYLATESTEVENTS_MODULE_VIEW_PATH;
    return $arr;
}, 10, 3);

// Start application
new ModularityLatestEvents\App();

