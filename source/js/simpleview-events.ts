/**
 * Modularity Latest Events - TypeScript Rendering
 * Fetches and renders event cards from SimpleView/Typesense API
 */

import { mockEvents, SimpleViewEvent } from './mockData';

/**
 * Simulate API fetch with mock data
 * In production, this will fetch from Typesense API
 */
async function fetchEvents(): Promise<SimpleViewEvent[]> {
    // Simulate network delay to show loading state
    await new Promise((resolve) => setTimeout(resolve, 800));
    return mockEvents;
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text: string): string {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Convert icon name to FontAwesome class
 * Supports both Material icon names and FontAwesome class names
 */
function getFontAwesomeClass(iconName: string): string {
    if (!iconName) {
        return 'fas fa-circle';
    }

    // If already a FontAwesome class (contains fa- or starts with fas/far/fab), use as-is
    if (iconName.includes('fa-') || /^(fas|far|fab|fal|fad)\s/.test(iconName)) {
        // If it doesn't start with a style prefix, add 'fas' (solid) as default
        if (!/^(fas|far|fab|fal|fad)\s/.test(iconName)) {
            return `fas ${iconName}`;
        }
        return iconName;
    }

    // Map common Material icon names to FontAwesome equivalents
    const iconMap: Record<string, string> = {
        'calendar_today': 'fas fa-calendar',
        'calendar': 'fas fa-calendar',
        'event': 'fas fa-calendar-alt',
        'location_on': 'fas fa-map-marker-alt',
        'location': 'fas fa-map-marker-alt',
        'place': 'fas fa-map-marker-alt',
        'category': 'fas fa-tag',
        'label': 'fas fa-tag',
        'folder': 'fas fa-folder',
        'description': 'fas fa-file-alt',
        'list': 'fas fa-list',
    };

    // Convert Material icon name format (snake_case) to check map
    const normalizedName = iconName.toLowerCase().replace(/\s+/g, '_');
    
    return iconMap[normalizedName] || `fas fa-${normalizedName.replace(/_/g, '-')}`;
}

/**
 * Render FontAwesome icon
 */
function renderIcon(iconClass: string, iconColor: string): string {
    const faClass = getFontAwesomeClass(iconClass);
    const colorStyle = iconColor ? `style="color: ${escapeHtml(iconColor)};"` : '';
    return `<i class="${escapeHtml(faClass)}" ${colorStyle} aria-hidden="true"></i>`;
}

/**
 * Render a single event card
 */
function renderEventCard(
    event: SimpleViewEvent,
    dateIcon: string,
    locationIcon: string,
    categoryIcon: string,
    iconColor: string
): string {
    return `
        <article class="c-event-card">
            <a href="${escapeHtml(event.link)}" class="c-event-card__link">
                <div class="c-event-card__image-wrapper">
                    <img 
                        src="${escapeHtml(event.image)}" 
                        alt="${escapeHtml(event.title)}"
                        class="c-event-card__image"
                        loading="lazy"
                    />
                    <div class="c-event-card__badge">
                        ${escapeHtml(event.badgeDate)}
                    </div>
                </div>
                <div class="c-event-card__content">
                    <h3 class="c-event-card__title">${escapeHtml(event.title)}</h3>
                    <div class="c-event-card__meta">
                        <div class="c-event-card__meta-item">
                            ${renderIcon(dateIcon, iconColor)}
                            <span>${escapeHtml(event.dateSpan)}</span>
                        </div>
                        <div class="c-event-card__meta-item">
                            ${renderIcon(locationIcon, iconColor)}
                            <span>${escapeHtml(event.location)}</span>
                        </div>
                        <div class="c-event-card__meta-item">
                            ${renderIcon(categoryIcon, iconColor)}
                            <span>${escapeHtml(event.category)}</span>
                        </div>
                    </div>
                </div>
            </a>
        </article>
    `;
}

/**
 * Show error state
 */
function showError(container: HTMLElement, message: string): void {
    container.innerHTML = `
        <div class="c-event-card__error">
            <p>${message}</p>
        </div>
    `;
}

/**
 * Initialize the module
 */
function initSimpleViewEvents(): void {
    const containers = document.querySelectorAll<HTMLElement>('[data-simpleview-events]');

    containers.forEach((container) => {
        // Get icon values from data attributes
        const dateIcon = container.getAttribute('data-date-icon') || 'calendar_today';
        const locationIcon = container.getAttribute('data-location-icon') || 'location_on';
        const categoryIcon = container.getAttribute('data-category-icon') || 'category';
        const iconColor = container.getAttribute('data-icon-color') || '#666666';

        // Fetch and render events
        fetchEvents()
            .then((events) => {
                // Remove skeleton loader
                const skeleton = container.querySelector('.c-event-card__skeleton-wrapper');
                if (skeleton) {
                    skeleton.remove();
                }

                // Render event cards with icon classes and color
                const cardsHtml = events
                    .map((event) => renderEventCard(event, dateIcon, locationIcon, categoryIcon, iconColor))
                    .join('');
                container.insertAdjacentHTML('beforeend', cardsHtml);
            })
            .catch((error) => {
                console.error('Failed to fetch events:', error);
                showError(container, 'Kunde inte ladda evenemang. Försök igen senare.');
            });
    });
}

// Initialize on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSimpleViewEvents);
} else {
    initSimpleViewEvents();
}

