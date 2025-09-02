import DOMPurify from 'dompurify';

export const sanitizeInput = (input) => {
    return DOMPurify.sanitize(input, { ALLOWED_TAGS: [] });
};

export const validateInput = (value, type) => {
    const patterns = {
        email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
        password: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&]{8,}$/,
        name: /^[a-zA-ZÀ-ÿ\s'-]{2,50}$/
    };

    return patterns[type] ? patterns[type].test(value) : true;
};