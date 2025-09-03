import DOMPurify from 'dompurify';

export const sanitizeInput = (input) => {
    // return DOMPurify.sanitize(input, {
    //     ALLOWED_TAGS: [],
    //     ALLOWED_ATTR: [],
    //     KEEP_CONTENT: true
    // });
    console.log("input : ", input);
    return input;
};

export const validateInput = (value, type) => {
    const patterns = {
        email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
        password: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&\-]{12,}$/,
        name: /^[a-zA-ZÀ-ÿ\s'-]{2,50}$/
    };

    return patterns[type] ? patterns[type].test(value) : true;
};

export const getValidationError = (value, type) => {
    const errors = {
        email: "Format d'email invalide",
        password: "Le mot de passe doit contenir au moins 12 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial (@$!%*?&).",
        name: "Le nom doit contenir entre 2 et 50 caractères (lettres, accents, espaces, tirets et apostrophes uniquement)"
    };

    if (!validateInput(value, type)) {
        return errors[type] || "Format invalide";
    }
    return null;
};