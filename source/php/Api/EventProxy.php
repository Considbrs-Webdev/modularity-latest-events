<?php

declare(strict_types=1);

namespace ModularityLatestEvents\Api;

class EventProxy
{
    private string $apiUrl;
    private string $apiToken;
    private string $siteUrl;

    public function __construct()
    {
        // Prioritize WordPress options over .env file
        $optionUrl   = get_option('modularity_latest_events_api_url', '');
        $optionToken = get_option('modularity_latest_events_api_token', '');

        // Fall back to .env if options are empty
        if (empty($optionUrl) || empty($optionToken)) {
            $env = $this->loadEnv();
            $this->apiUrl   = rtrim($env['VISITPITEA_API_URL'] ?? '', '/');
            $this->apiToken = $env['VISITPITEA_API_TOKEN'] ?? '';
        } else {
            $this->apiUrl   = rtrim($optionUrl, '/');
            $this->apiToken = $optionToken;
        }

        $this->siteUrl = rtrim(preg_replace('#/api$#', '', $this->apiUrl), '/');

        add_action('wp_ajax_latest_events', [$this, 'handleRequest']);
        add_action('wp_ajax_nopriv_latest_events', [$this, 'handleRequest']);
    }

    public function handleRequest(): void
    {
        $this->sendNoCacheHeaders();

        if (empty($this->apiUrl) || empty($this->apiToken)) {
            wp_send_json_error('Event API is not configured.', 500);
        }

        $perPage = min(abs(intval($_GET['per_page'] ?? 4)), 12);
        $cacheKey = 'latest_events_' . $perPage;
        $cached = get_transient($cacheKey);

        if ($cached !== false) {
            wp_send_json($cached);
        }

        $response = $this->apiGet('/events', [
            'lang'     => 'sv',
            'per_page' => $perPage,
            'sort'     => 'date',
            'order'    => 'asc',
        ]);

        if ($response === null) {
            wp_send_json_error('Could not fetch events.', 502);
        }

        $events = $response['data'] ?? [];
        $result = [];

        foreach ($events as $event) {
            $imageUrl = $this->resolveImage(intval($event['image'] ?? 0));
            $categoryIds = $event['category_ids'] ?? [];
            $categoryLabel = $this->resolveCategoryLabel($categoryIds);

            $result[] = [
                'id'        => $event['id'],
                'title'     => $event['title'] ?? '',
                'image'     => $imageUrl,
                'badgeDate' => $this->extractBadgeDate($event['event_date'] ?? ''),
                'dateSpan'  => $this->buildDateSpan($event),
                'location'  => trim($event['location'] ?? ''),
                'category'  => $categoryLabel,
                'link'      => $this->siteUrl . '/events/' . ($event['slug'] ?? ''),
            ];
        }

        set_transient($cacheKey, $result, 2 * HOUR_IN_SECONDS);
        wp_send_json($result);
    }

    /**
     * Extract a short badge date like "21 mar" from strings like
     * "Lördag, 21 mars", "26–28 juni 2026", or "2 maj 2025 – 30 sep 2027".
     */
    private function extractBadgeDate(string $eventDate): string
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

        // "Lördag, 21 mars" -> "21 mar"
        if (preg_match('/(\d{1,2})\s+(januari|februari|mars|april|maj|juni|juli|augusti|september|oktober|november|december)/iu', $eventDate, $m)) {
            $shortMonth = $months[mb_strtolower($m[2])] ?? mb_substr($m[2], 0, 3);
            return $m[1] . ' ' . $shortMonth;
        }

        // "26–28 juni 2026" -> "26 jun"
        if (preg_match('/(\d{1,2})[–\-]\d{1,2}\s+(januari|februari|mars|april|maj|juni|juli|augusti|september|oktober|november|december)/iu', $eventDate, $m)) {
            $shortMonth = $months[mb_strtolower($m[2])] ?? mb_substr($m[2], 0, 3);
            return $m[1] . ' ' . $shortMonth;
        }

        return mb_substr($eventDate, 0, 6);
    }

    private function buildDateSpan(array $event): string
    {
        $span = $event['event_date'] ?? '';

        if (!empty($event['event_time'])) {
            $span .= ' ' . $event['event_time'];
        }

        return $span;
    }

    private function resolveImage(int $imageId): string
    {
        if ($imageId <= 0) {
            return '';
        }

        $data = $this->apiGet('/attachments/' . $imageId);

        return $data['sizes']['large']['url']
            ?? $data['sizes']['medium_large']['url']
            ?? $data['url']
            ?? '';
    }

    /**
     * Fetch category names from API and return as comma-separated string.
     *
     * @param array<int, int> $categoryIds
     */
    private function resolveCategoryLabel(array $categoryIds): string
    {
        if (empty($categoryIds)) {
            return '';
        }

        $names = [];
        foreach ($categoryIds as $id) {
            $id = (int) $id;
            if ($id <= 0) {
                continue;
            }
            $data = $this->apiGet('/categories/' . $id);
            $name = $data['name'] ?? null;
            if (is_string($name) && $name !== '') {
                $names[] = $name;
            }
        }

        return implode(', ', $names);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function apiGet(string $path, array $query = []): ?array
    {
        $url = $this->apiUrl . $path;

        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        $response = wp_remote_get($url, [
            'timeout' => 10,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Accept'        => 'application/json',
            ],
        ]);

        if (is_wp_error($response)) {
            return null;
        }

        $code = wp_remote_retrieve_response_code($response);
        if ($code < 200 || $code >= 300) {
            return null;
        }

        $body = wp_remote_retrieve_body($response);
        $decoded = json_decode($body, true);

        return is_array($decoded) ? $decoded : null;
    }

    private function sendNoCacheHeaders(): void
    {
        if (!headers_sent()) {
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Pragma: no-cache');
            header('Expires: 0');
            header('X-LiteSpeed-Cache-Control: no-cache');
        }
    }

    /**
     * @return array<string, string>
     */
    private function loadEnv(): array
    {
        $envFile = MODULARITYLATESTEVENTS_PATH . '.env';

        if (!file_exists($envFile) || !is_readable($envFile)) {
            return [];
        }

        $vars = [];
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || $line[0] === '#') {
                continue;
            }

            if (strpos($line, '=') === false) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $vars[trim($key)] = trim($value);
        }

        return $vars;
    }
}
