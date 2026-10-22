<?php

declare(strict_types=1);

namespace ModularityLatestEvents\Tests;

final class WpState
{
    /** @var array<string, mixed> */
    public static array $options = [];

    /** @var array<string, mixed> */
    public static array $transients = [];

    /** @var array<string, int> */
    public static array $transientTtl = [];

    /** @var array<int, array{url: string, args: array<string, mixed>}> */
    public static array $requests = [];

    public static int $remoteCode = 200;

    public static mixed $remoteBody = null;

    public static ?\WP_Error $remoteError = null;

    public static function reset(): void
    {
        self::$options = [];
        self::$transients = [];
        self::$transientTtl = [];
        self::$requests = [];
        self::$remoteCode = 200;
        self::$remoteBody = null;
        self::$remoteError = null;
    }
}
