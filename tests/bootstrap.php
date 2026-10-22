<?php

declare(strict_types=1);

$root = sys_get_temp_dir() . '/modularity-latest-events-phpunit';
if (!is_dir($root) && !mkdir($root, 0777, true) && !is_dir($root)) {
    fwrite(STDERR, "Could not create PHPUnit temp directory\n");
    exit(1);
}

$envFile = $root . '/.env';
if (is_file($envFile)) {
    unlink($envFile);
}

if (!defined('MODULARITYLATESTEVENTS_PATH')) {
    define('MODULARITYLATESTEVENTS_PATH', $root . '/');
}

require dirname(__DIR__) . '/vendor/autoload.php';
require __DIR__ . '/wp-stubs.php';
