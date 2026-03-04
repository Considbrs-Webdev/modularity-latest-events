<?php

/**
 * Standalone event proxy — bypasses WordPress entirely.
 * Called directly from JS: /wp-content/plugins/modularity-latest-events/event-proxy.php
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');
header('X-LiteSpeed-Cache-Control: no-cache');

$envFile = __DIR__ . '/.env';

if (!file_exists($envFile) || !is_readable($envFile)) {
    http_response_code(500);
    echo json_encode(['error' => 'Environment file not found.']);
    exit;
}

$env = [];
foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    $line = trim($line);
    if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
        continue;
    }
    [$key, $value] = explode('=', $line, 2);
    $env[trim($key)] = trim($value);
}

$apiUrl   = rtrim($env['VISITPITEA_API_URL'] ?? '', '/');
$apiToken = $env['VISITPITEA_API_TOKEN'] ?? '';
$siteUrl  = rtrim(preg_replace('#/api$#', '', $apiUrl), '/');

if (empty($apiUrl) || empty($apiToken)) {
    http_response_code(500);
    echo json_encode(['error' => 'Event API is not configured.']);
    exit;
}

$perPage = min(abs(intval($_GET['per_page'] ?? 4)), 12);

$events = apiGet($apiUrl . '/events?' . http_build_query([
    'lang'     => 'sv',
    'per_page' => $perPage,
    'sort'     => 'date',
    'order'    => 'asc',
]), $apiToken);

if ($events === null) {
    http_response_code(502);
    echo json_encode(['error' => 'Could not fetch events.']);
    exit;
}

$result = [];

foreach ($events['data'] ?? [] as $event) {
    $imageUrl = '';
    $imageId  = intval($event['image'] ?? 0);

    if ($imageId > 0) {
        $attachment = apiGet($apiUrl . '/attachments/' . $imageId, $apiToken);
        $imageUrl   = $attachment['sizes']['large']['url']
                   ?? $attachment['sizes']['medium_large']['url']
                   ?? $attachment['url']
                   ?? '';
    }

    $result[] = [
        'id'        => $event['id'],
        'title'     => $event['title'] ?? '',
        'image'     => $imageUrl,
        'badgeDate' => extractBadgeDate($event['event_date'] ?? ''),
        'dateSpan'  => buildDateSpan($event),
        'link'      => $siteUrl . '/events/' . ($event['slug'] ?? ''),
    ];
}

echo json_encode($result);
exit;


function apiGet(string $url, string $token): ?array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $token,
            'Accept: application/json',
        ],
    ]);

    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($body === false || $code < 200 || $code >= 300) {
        return null;
    }

    $decoded = json_decode($body, true);
    return is_array($decoded) ? $decoded : null;
}

function extractBadgeDate(string $eventDate): string
{
    if (empty($eventDate)) {
        return '';
    }

    $months = [
        'januari' => 'jan', 'februari' => 'feb', 'mars' => 'mar',
        'april' => 'apr', 'maj' => 'maj', 'juni' => 'jun',
        'juli' => 'jul', 'augusti' => 'aug', 'september' => 'sep',
        'oktober' => 'okt', 'november' => 'nov', 'december' => 'dec',
    ];

    if (preg_match('/(\d{1,2})\s+(januari|februari|mars|april|maj|juni|juli|augusti|september|oktober|november|december)/iu', $eventDate, $m)) {
        $shortMonth = $months[mb_strtolower($m[2])] ?? mb_substr($m[2], 0, 3);
        return $m[1] . ' ' . $shortMonth;
    }

    if (preg_match('/(\d{1,2})[–\-]\d{1,2}\s+(januari|februari|mars|april|maj|juni|juli|augusti|september|oktober|november|december)/iu', $eventDate, $m)) {
        $shortMonth = $months[mb_strtolower($m[2])] ?? mb_substr($m[2], 0, 3);
        return $m[1] . ' ' . $shortMonth;
    }

    return mb_substr($eventDate, 0, 6);
}

function buildDateSpan(array $event): string
{
    $span = $event['event_date'] ?? '';

    if (!empty($event['event_time'])) {
        $span .= ' ' . $event['event_time'];
    }

    return $span;
}
