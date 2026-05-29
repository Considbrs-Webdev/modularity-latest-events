declare const modLatestEvents: { ajaxUrl: string };

interface LatestEvent {
    id: number;
    title: string;
    image: string;
    badgeDate: string;
    dateSpan: string;
    location?: string;
    category?: string;
    link: string;
}

async function fetchEvents(): Promise<LatestEvent[]> {
    const url = new URL(modLatestEvents.ajaxUrl, window.location.origin);
    url.searchParams.set('action', 'latest_events');
    url.searchParams.set('per_page', '4');
    url.searchParams.set('_nocache', Date.now().toString());

    const response = await fetch(url.toString(), { cache: 'no-store' });

    if (!response.ok) {
        throw new Error(`Ajax HTTP ${response.status}`);
    }

    return response.json();
}

/** Decode HTML entities so already-encoded API data is normalized before we escape. */
function decodeHtml(html: string): string {
    if (!html) return html;
    const div = document.createElement('div');
    div.innerHTML = html;
    return div.textContent ?? '';
}

function escapeHtml(text: string): string {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/** Safe text for injection: decode then escape to handle pre-encoded API data. */
function safeHtml(text: string): string {
    return escapeHtml(decodeHtml(text));
}

function getFontAwesomeClass(iconName: string): string {
    if (!iconName) {
        return 'fas fa-circle';
    }

    if (iconName.includes('fa-') || /^(fas|far|fab|fal|fad)\s/.test(iconName)) {
        if (!/^(fas|far|fab|fal|fad)\s/.test(iconName)) {
            return `fas ${iconName}`;
        }
        return iconName;
    }

    const iconMap: Record<string, string> = {
        calendar_today: 'fas fa-calendar',
        calendar: 'fas fa-calendar',
        event: 'fas fa-calendar-alt',
        location_on: 'fas fa-map-marker-alt',
        location: 'fas fa-map-marker-alt',
        place: 'fas fa-map-marker-alt',
        category: 'fas fa-tag',
        label: 'fas fa-tag',
        folder: 'fas fa-folder',
        description: 'fas fa-file-alt',
        list: 'fas fa-list',
    };

    const normalizedName = iconName.toLowerCase().replace(/\s+/g, '_');
    return iconMap[normalizedName] || `fas fa-${normalizedName.replace(/_/g, '-')}`;
}

function renderIcon(iconClass: string, iconColor: string): string {
    const faClass = getFontAwesomeClass(iconClass);
    const colorStyle = iconColor ? `style="color: ${escapeHtml(iconColor)};"` : '';
    return `<i class="${escapeHtml(faClass)} c-event-card__icon" ${colorStyle} aria-hidden="true"></i>`;
}

function renderEventCard(
    event: LatestEvent,
    dateIcon: string,
    iconColor: string
): string {
    const imageHtml = event.image
        ? `<img 
                src="${escapeHtml(event.image)}" 
                alt="${safeHtml(event.title)}"
                class="c-event-card__image"
                loading="lazy"
            />`
        : '';

    return `
        <li class="c-event-card">
            <div class="c-event-card__image-wrapper">
                    ${imageHtml}
                    ${event.badgeDate ? `<div class="c-event-card__badge">${safeHtml(event.badgeDate)}</div>` : ''}
            </div>
            <div class="c-event-card__content">
                    <h3 class="c-event-card__title">
                        <a href="${escapeHtml(event.link)}" class="c-event-card__link">${safeHtml(event.title)}</a>
                    </h3>
                    <div class="c-event-card__meta">
                        ${event.dateSpan ? `
                        <div class="c-event-card__meta-item">
                            ${renderIcon(dateIcon, iconColor)}
                            <span>${safeHtml(event.dateSpan)}</span>
                        </div>` : ''}
                        ${event.location ? `
                        <div class="c-event-card__meta-item">
                            ${renderIcon('location_on', iconColor)}
                            <span>${safeHtml(event.location)}</span>
                        </div>` : ''}
                    </div>
            </div>
        </li>
    `;
}

function showError(container: HTMLElement, message: string): void {
    container.innerHTML = `
        <li class="c-event-card__error" role="status">
            <p>${message}</p>
        </li>
    `;
}

function initLatestEvents(): void {
    const containers = document.querySelectorAll<HTMLElement>('[data-simpleview-events]');

    containers.forEach((container) => {
        const dateIcon = container.getAttribute('data-date-icon') || 'calendar_today';
        const iconColor = container.getAttribute('data-icon-color') || '#666666';

        fetchEvents()
            .then((events) => {
                container
                    .querySelectorAll<HTMLElement>('.c-event-card--skeleton')
                    .forEach((el) => el.remove());

                const cardsHtml = events
                    .map((event) => renderEventCard(event, dateIcon, iconColor))
                    .join('');
                container.insertAdjacentHTML('beforeend', cardsHtml);
            })
            .catch((error) => {
                console.error('Failed to fetch events:', error);
                showError(container, 'Kunde inte ladda evenemang. Försök igen senare.');
            });
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initLatestEvents);
} else {
    initLatestEvents();
}
