import { useEffect } from 'react';
import { useLocation } from 'react-router-dom';
import AnalyticsService from '../services/analyticsService';

export const useAnalytics = () => {
    const location = useLocation();
    useEffect(() => {
        AnalyticsService.trackPageView();
    }, [location.pathname]);

    return {
        trackClick: (element, additionalData = {}) => {
            AnalyticsService.trackClick(element);
            if (Object.keys(additionalData).length > 0) {
                AnalyticsService.trackEvent({
                    eventType: 'custom_click',
                    element,
                    ...additionalData
                }).then(r => r);
            }
        },
        trackEvent: AnalyticsService.trackEvent,
        trackFormSubmit: (formName) => {
            AnalyticsService.trackEvent({
                eventType: 'form_submit',
                formName
            }).then(r => r);
        },
        trackError: (error, context) => {
            AnalyticsService.trackEvent({
                eventType: 'error',
                error: error.message,
                context
            }).then(r => r);
        }
    };
};
