import { useState } from 'react';
import InputWithLabel from '../../molecules/InputWithLabel/InputWithLabel';
import Button from '../../atoms/Button/Button';
import Error from "../../atoms/Error";
import styles from './RegisterForm.module.scss';
import apiClient from "../../../utils/apiClient";
import { sanitizeInput, validateInput, getValidationError } from '../../../utils/xssProtection';

const RegisterForm = ({ onNext }) => {
    const [data, setData] = useState({ email: '', password: '', firstName: '', lastName: '' });
    const [error, setError] = useState(null);
    const [fieldErrors, setFieldErrors] = useState({});
    const [isLoading, setIsLoading] = useState(false);

    const handleChange = e => {
        const { name, value } = e.target;
        const cleanValue = sanitizeInput(value);

        // Validation en temps réel avec messages cohérents backend
        const validationTypes = {
            email: 'email',
            password: 'password',
            firstName: 'name',
            lastName: 'name'
        };

        const fieldError = validationTypes[name]
            ? getValidationError(cleanValue, validationTypes[name])
            : null;

        setFieldErrors(prev => ({
            ...prev,
            [name]: fieldError
        }));

        setData(prev => ({ ...prev, [name]: cleanValue }));
    };

    const handleSubmit = async e => {
        e.preventDefault();
        console.log('handleSubmit appelé'); // Debug
        console.log('Data:', data); // Debug
        setError(null);

        // Validation complète avant soumission
        const validationErrors = {};
        validationErrors.firstName = getValidationError(data.firstName, 'name');
        validationErrors.lastName = getValidationError(data.lastName, 'name');
        validationErrors.email = getValidationError(data.email, 'email');
        validationErrors.password = getValidationError(data.password, 'password');

        console.log('Validation errors:', validationErrors); // Debug

        const hasErrors = Object.values(validationErrors).some(error => error !== null);
        console.log('Has errors:', hasErrors); // Debug

        if (hasErrors) {
            setFieldErrors(validationErrors);
            console.log('Arrêt à cause des erreurs de validation'); // Debug
            return;
        }

        setIsLoading(true);
        console.log('Début de la vérification email'); // Debug

        try {
            const res = await apiClient.get(`/users/verify?email=${encodeURIComponent(data.email)}`);
            console.log('Réponse vérification email:', res); // Debug
            if (res.exists === true) {
                setError("Cet email est déjà utilisé.");
                console.log('Email déjà utilisé'); // Debug
                return;
            }
        } catch (err) {
            console.log('Erreur API:', err); // Debug
            setError("Erreur lors de la vérification de l'email.");
            return;
        } finally {
            setIsLoading(false);
        }

        console.log('Appel onNext avec:', data); // Debug
        onNext(data);
    };

    return (
        <>
            <Error children={error}/>
            <p className={styles.registerInformationLabel}>
                Commencez par renseigner vos informations personnelles pour accéder à l'application.
            </p>
            <form className={styles.registerFormContainer} onSubmit={handleSubmit}>
                <h2 className={styles.formTitle}>
                    <img src="/icons/user.svg" alt="Utilisateur"/>
                    Informations personnelles
                </h2>
                <InputWithLabel
                    placeholder="Votre prénom"
                    label="Prénom"
                    id="firstName"
                    name="firstName"
                    value={data.firstName}
                    onChange={handleChange}
                    error={fieldErrors.firstName}
                />
                <InputWithLabel
                    placeholder="Votre nom"
                    label="Nom"
                    id="lastName"
                    name="lastName"
                    value={data.lastName}
                    onChange={handleChange}
                    error={fieldErrors.lastName}
                />
                <InputWithLabel
                    placeholder="exemple@email.com"
                    label="Email"
                    id="email"
                    name="email"
                    value={data.email}
                    onChange={handleChange}
                    error={fieldErrors.email}
                />
                <InputWithLabel
                    placeholder="Votre mot de passe"
                    label="Mot de passe"
                    id="password"
                    name="password"
                    type="password"
                    value={data.password}
                    onChange={handleChange}
                    error={fieldErrors.password}
                />
                <Button type="submit" isLoading={isLoading}>Suivant</Button>
            </form>
        </>
    );
};

export default RegisterForm;