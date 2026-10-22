<?php

declare(strict_types=1);

use ModularityLatestEvents\Tests\WpState;

if (!defined('MINUTE_IN_SECONDS')) {
    define('MINUTE_IN_SECONDS', 60);
}

if (!defined('HOUR_IN_SECONDS')) {
    define('HOUR_IN_SECONDS', 3600);
}

if (!class_exists('WP_Error')) {
    class WP_Error
    {
        public function __construct(
            public string $code = '',
            public string $message = '',
            public mixed $data = '',
        ) {
        }
    }
}

function add_action(string $hook, callable $callback, int $priority = 10, int $acceptedArgs = 1): void
{
}

function get_option(string $option, mixed $default = false): mixed
{
    return WpState::$options[$option] ?? $default;
}

function get_transient(string $key): mixed
{
    return WpState::$transients[$key] ?? false;
}

function set_transient(string $key, mixed $value, int $expiration = 0): bool
{
    WpState::$transients[$key] = $value;
    WpState::$transientTtl[$key] = $expiration;

    return true;
}

function is_wp_error(mixed $thing): bool
{
    return $thing instanceof WP_Error;
}

function wp_remote_get(string $url, array $args = []): mixed
{
    WpState::$requests[] = ['url' => $url, 'args' => $args];

    if (WpState::$remoteError instanceof WP_Error) {
        return WpState::$remoteError;
    }

    $body = WpState::$remoteBody;
    if (is_array($body)) {
        $body = json_encode($body, JSON_THROW_ON_ERROR);
    }

    return [
        'response' => ['code' => WpState::$remoteCode],
        'body' => $body ?? '',
    ];
}

function wp_remote_retrieve_response_code(mixed $response): int
{
    return (int) ($response['response']['code'] ?? 0);
}

function wp_remote_retrieve_body(mixed $response): string
{
    return (string) ($response['body'] ?? '');
}
