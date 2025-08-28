const API_URL = process.env.REACT_APP_API_URL || 'http://localhost:8000';

class AnalyticsService {
    static async trackEvent(data) {
        try {
            const eventData = {
                url: window.location.pathname,
                timestamp: new Date().toISOString(),
                userAgent: navigator.userAgent,
                referrer: document.referrer,
                screenWidth: window.screen.width,
                screenHeight: window.screen.height,
                language: navigator.language,
                pageTitle: document.title,
                deviceType: this.getDeviceType(),
                ...data // permet d'override ou d'ajouter des données
            };

            await fetch(`${API_URL}/analytics`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(eventData)
            });
        } catch (error) {
            console.warn('Analytics tracking failed:', error);
        }
    }

    static getDeviceType() {
        const width = window.screen.width;
        if (width <= 768) return 'Mobile';
        if (width <= 1024) return 'Tablet';
        return 'Desktop';
    }

    static trackPageView() {
        this.trackEvent({
            eventType: 'page_view',
            loadTime: performance.now()
        });
    }

    static trackClick(element) {
        this.trackEvent({
            eventType: 'click',
            elementType: element
        });
    }
}

export default AnalyticsService;
