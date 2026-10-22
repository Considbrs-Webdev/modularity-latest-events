<?php

declare(strict_types=1);

namespace ModularityLatestEvents\Tests;

use ModularityLatestEvents\Api\EventProxy;
use PHPUnit\Framework\TestCase;

final class EventProxyTest extends TestCase
{
    private const API_URL = 'https://events.example.test/api';

    private const API_TOKEN = 'synthetic-token';

    protected function setUp(): void
    {
        WpState::reset();
    }

    public function testEditorPreviewFailsWhenTheApiIsNotConfigured(): void
    {
        $result = (new EventProxy())->getEditorPreviewEvents();

        $this->assertInstanceOf(\WP_Error::class, $result);
        $this->assertSame('latest_events_unconfigured', $result->code);
        $this->assertSame([], WpState::$requests);
    }

    public function testEditorPreviewReturnsTheCachedPreviewWithoutCallingTheApi(): void
    {
        WpState::$transients['latest_events_editor_preview_4'] = [
            ['title' => 'Cached event'],
        ];
        $this->configureApi();

        $result = (new EventProxy())->getEditorPreviewEvents();

        $this->assertSame([['title' => 'Cached event']], $result);
        $this->assertSame([], WpState::$requests);
    }

    public function testEditorPreviewSlicesThePublicListCacheToFourEvents(): void
    {
        WpState::$transients['latest_events_4'] = [
            ['title' => 'One'],
            ['title' => 'Two'],
            ['title' => 'Three'],
            ['title' => 'Four'],
            ['title' => 'Five'],
        ];
        $this->configureApi();

        $result = (new EventProxy())->getEditorPreviewEvents();

        $this->assertCount(4, $result);
        $this->assertSame('Four', $result[3]['title']);
        $this->assertArrayNotHasKey(4, $result);
        $this->assertSame($result, WpState::$transients['latest_events_editor_preview_4']);
        $this->assertSame(10 * MINUTE_IN_SECONDS, WpState::$transientTtl['latest_events_editor_preview_4']);
        $this->assertSame([], WpState::$requests);
    }

    public function testEditorPreviewMapsASyntheticEventAndSkipsMalformedRows(): void
    {
        $this->configureApi();
        WpState::$remoteBody = [
            'data' => [
                'not-an-event',
                [
                    'title' => 'Synthetic concert',
                    'event_date' => 'Lördag, 21 mars',
                    'event_time' => '19:00',
                    'location' => ' Studio ',
                    'slug' => 'synth-slug',
                ],
            ],
        ];

        $result = (new EventProxy())->getEditorPreviewEvents();

        $this->assertSame([
            [
                'title' => 'Synthetic concert',
                'image' => '',
                'badgeDate' => '21 mar',
                'dateSpan' => 'Lördag, 21 mars 19.00',
                'location' => 'Studio',
                'link' => 'https://events.example.test/events/synth-slug',
            ],
        ], $result);

        $request = WpState::$requests[0];
        $this->assertStringContainsString('https://events.example.test/api/events?', $request['url']);
        $this->assertStringContainsString('per_page=4', $request['url']);
        $this->assertStringContainsString('lang=sv', $request['url']);
        $this->assertSame(2, $request['args']['timeout']);
        $this->assertSame('Bearer ' . self::API_TOKEN, $request['args']['headers']['Authorization']);
    }

    public function testEditorPreviewKeepsOnlyFourEventsFromTheApi(): void
    {
        $this->configureApi();
        $rows = [];
        foreach (['One', 'Two', 'Three', 'Four', 'Five'] as $title) {
            $rows[] = ['title' => $title, 'slug' => 'event'];
        }
        WpState::$remoteBody = ['data' => $rows];

        $result = (new EventProxy())->getEditorPreviewEvents();

        $this->assertCount(4, $result);
        $this->assertSame('Four', $result[3]['title']);
    }

    public function testEditorPreviewUsesTheFirstDayOfADateRange(): void
    {
        $this->configureApi();
        WpState::$remoteBody = [
            'data' => [
                ['title' => 'Festival', 'event_date' => '26–28 juni 2026', 'slug' => 'festival'],
            ],
        ];

        $result = (new EventProxy())->getEditorPreviewEvents();

        $this->assertSame('26 jun', $result[0]['badgeDate']);
    }

    public function testEditorPreviewFailsWhenTheApiRequestFails(): void
    {
        $this->configureApi();
        WpState::$remoteError = new \WP_Error('http_request_failed', 'synthetic failure');

        $result = (new EventProxy())->getEditorPreviewEvents();

        $this->assertInstanceOf(\WP_Error::class, $result);
        $this->assertSame('latest_events_unavailable', $result->code);
    }

    public function testEditorPreviewFailsWhenTheApiReturnsAnErrorStatus(): void
    {
        $this->configureApi();
        WpState::$remoteCode = 502;
        WpState::$remoteBody = ['data' => []];

        $result = (new EventProxy())->getEditorPreviewEvents();

        $this->assertInstanceOf(\WP_Error::class, $result);
        $this->assertSame('latest_events_unavailable', $result->code);
    }

    public function testEditorPreviewReturnsAnEmptyListWhenTheApiHasNoEvents(): void
    {
        $this->configureApi();
        WpState::$remoteBody = ['data' => []];

        $this->assertSame([], (new EventProxy())->getEditorPreviewEvents());
    }

    private function configureApi(): void
    {
        WpState::$options['modularity_latest_events_api_url'] = self::API_URL;
        WpState::$options['modularity_latest_events_api_token'] = self::API_TOKEN;
    }
}
