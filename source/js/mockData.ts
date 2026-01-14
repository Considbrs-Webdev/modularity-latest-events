/**
 * Mock data for SimpleView events
 * This will be replaced with actual Typesense API calls later
 */

export interface SimpleViewEvent {
    id: string;
    title: string;
    image: string;
    badgeDate: string;    // Pre-formatted for badge: "09 sep"
    dateSpan: string;     // Pre-formatted date range: "Tisdag, 16 september" or "15-20 september"
    location: string;
    category: string;
    link: string;
}

export const mockEvents: SimpleViewEvent[] = [
    {
        id: '1',
        title: 'Konsert i badhusparken',
        image: 'https://images.unsplash.com/photo-1513475382585-d06e58bcb0e0?w=800&h=600&fit=crop',
        badgeDate: '09 sep',
        dateSpan: 'Tisdag, 16 september',
        location: 'Badhusparken',
        category: 'Kategori',
        link: '#',
    },
    {
        id: '2',
        title: 'Rallycross SM på Piteå Motorstadion',
        image: 'https://images.unsplash.com/photo-1513475382585-d06e58bcb0e0?w=800&h=600&fit=crop',
        badgeDate: '09 sep',
        dateSpan: 'Tisdag, 16 september',
        location: 'Badhusparken',
        category: 'Kategori',
        link: '#',
    },
    {
        id: '3',
        title: 'Piteå Summer Games - PSG 2026',
        image: 'https://images.unsplash.com/photo-1513475382585-d06e58bcb0e0?w=800&h=600&fit=crop',
        badgeDate: '09 sep',
        dateSpan: 'Tisdag, 16 september',
        location: 'Badhusparken',
        category: 'Kategori',
        link: '#',
    },
    {
        id: '4',
        title: 'Öppen ateljé',
        image: 'https://images.unsplash.com/photo-1513475382585-d06e58bcb0e0?w=800&h=600&fit=crop',
        badgeDate: '09 sep',
        dateSpan: 'Tisdag, 16 september',
        location: 'Badhusparken',
        category: 'Kategori',
        link: '#',
    },
];

