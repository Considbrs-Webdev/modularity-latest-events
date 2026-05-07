<div class="mod-latest-events">
    @if (!$hideTitle && $postTitle)
    <div class="mod-latest-events__header">
            @typography([
                'element' => 'h2',
                'variant' => 'h2',
                'classList' => ['mod-latest-events__title']
            ])
            {{ $postTitle }}
            @endtypography
    </div>
    @endif

    <ul
        class="mod-latest-events__container"
        data-simpleview-events
        data-date-icon="{{ $dateIcon ?? 'calendar_today' }}"
        data-icon-color="{{ $iconColor ?? '#666666' }}"
    >
        {{-- Skeleton loader --}}
        @for ($i = 0; $i < 4; $i++)
            <li class="c-event-card c-event-card--skeleton">
                <div class="c-event-card__image-wrapper">
                    <div class="c-event-card__skeleton-image"></div>
                    <div class="c-event-card__badge c-event-card__badge--skeleton"></div>
                </div>
                <div class="c-event-card__content">
                    <div class="c-event-card__skeleton-title"></div>
                    <div class="c-event-card__skeleton-meta">
                        <div class="c-event-card__skeleton-line"></div>
                        <div class="c-event-card__skeleton-line"></div>
                        <div class="c-event-card__skeleton-line"></div>
                    </div>
                </div>
            </li>
        @endfor
    </ul>

    @if (!empty($eventsCalendarUrl))
    <div class="mod-latest-events__footer">
        <a href="{{ esc_url($eventsCalendarUrl) }}" class="mod-latest-events__link">
            {{ __('Till evenemangskalendern', 'modularity-latest-events') }}
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M12 4L11.293 4.707L13.586 7H2V8H13.586L11.293 10.293L12 11L15.5 7.5L12 4Z" fill="currentColor"/>
            </svg>
        </a>
    </div>
    @endif
</div>
