import { useState } from 'react';
import { useNavigate } from 'react-router';
import { useAuth } from '../../contexts/AuthContext';
import InputWithLabel from '../molecules/InputWithLabel';
import Button from '../atoms/Button/Button';
import Error from '../atoms/Error';
import apiClient from '../../utils/apiClient';

const LoginForm = () => {
    const [form, setForm] = useState({ email: '', password: '' });
    const [error, setError] = useState(null);
    const [loading, setLoading] = useState(false);
    const navigate = useNavigate();
    const { login } = useAuth();

    const handleChange = (e) => {
        setForm({ ...form, [e.target.name]: e.target.value });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setError(null);
        setLoading(true);

        try {
            const response = await apiClient.post('/login', {
                email: form.email,
                password: form.password
            });

            // Utiliser le contexte d'authentification pour stocker le token
            login(response.token);

            // Rediriger vers la page d'accueil
            navigate('/');
        } catch (err) {
            console.error('Erreur de connexion:', err.message);
            setError(err.response?.data?.detail || 'Échec de la connexion. Vérifiez vos identifiants.');
        } finally {
            setLoading(false);
        }
    };

    return (
        <>
            {error && <Error>{error}</Error>}
            <form onSubmit={handleSubmit}>
                <InputWithLabel
                    label="Email"
                    id="email"
                    name="email"
                    type="email"
                    value={form.email}
                    onChange={handleChange}
                    required
                />
                <InputWithLabel
                    label="Mot de passe"
                    id="password"
                    name="password"
                    type="password"
                    value={form.password}
                    onChange={handleChange}
                    required
                />
                <Button type="submit" disabled={loading}>
                    {loading ? 'Connexion en cours...' : 'Se connecter'}
                </Button>
            </form>
        </>
    );
};

export default LoginForm;