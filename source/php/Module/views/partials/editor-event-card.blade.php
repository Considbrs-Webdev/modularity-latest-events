<li class="c-event-card">
    <div class="c-event-card__image-wrapper">
        @if (!empty($event['image']))
            <img src="{{ $event['image'] }}" alt="" class="c-event-card__image">
        @endif
        @if (!empty($event['badgeDate']))
            <div class="c-event-card__badge">{{ $event['badgeDate'] }}</div>
        @endif
    </div>
    <div class="c-event-card__content">
        <h3 class="c-event-card__title">
            <a href="{{ $event['link'] ?? '#' }}" class="c-event-card__link">{{ $event['title'] ?? '' }}</a>
        </h3>
        <div class="c-event-card__meta">
            @if (!empty($event['dateSpan']))
                <div class="c-event-card__meta-item">
                    <svg class="c-event-card__icon" width="16" height="16" viewBox="0 0 16 16" aria-hidden="true" focusable="false" style="color: {{ $iconColor }};">
                        <path fill="currentColor" d="M5 1.5h1V3h4V1.5h1.5V3H13a1 1 0 0 1 1 1v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h1.5V1.5zM3.5 7v6h9V7h-9z"/>
                    </svg>
                    <span>{{ $event['dateSpan'] }}</span>
                </div>
            @endif
            @if (!empty($event['location']))
                <div class="c-event-card__meta-item">
                    <svg class="c-event-card__icon" width="16" height="16" viewBox="0 0 16 16" aria-hidden="true" focusable="false" style="color: {{ $iconColor }};">
                        <path fill="currentColor" d="M8 1.5a4.5 4.5 0 0 0-4.5 4.5C3.5 9.2 8 14.5 8 14.5s4.5-5.3 4.5-8.5A4.5 4.5 0 0 0 8 1.5zm0 6.2a1.7 1.7 0 1 1 0-3.4 1.7 1.7 0 0 1 0 3.4z"/>
                    </svg>
                    <span>{{ $event['location'] }}</span>
                </div>
            @endif
        </div>
    </div>
</li>
