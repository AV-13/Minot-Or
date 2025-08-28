import { createContext, useState, useEffect, useContext } from 'react';
import { jwtDecode } from 'jwt-decode';
import apiClient from '../utils/apiClient';
import {useNavigate} from "react-router";

const AuthContext = createContext();

export const AuthProvider = ({ children }) => {
    const [user, setUser] = useState(null);
    const [isLoading, setIsLoading] = useState(true);
    const navigate = useNavigate();

    // Fonction pour charger les détails complets de l'utilisateur
    const loadUserDetails = async (token) => {
        try {
            // Décoder d'abord le token pour les infos de base
            const decodedToken = jwtDecode(token);

            // Configurer le header d'autorisation
            apiClient.defaults.headers.common['Authorization'] = `Bearer ${token}`;

            // Récupérer les informations détaillées de l'utilisateur
            const userData = await apiClient.get('/users/me');

            // Si l'utilisateur a une entreprise, récupérer ses détails
            let companyData = null;
            if (userData.companyId) {
                companyData = await apiClient.get(`/companies/${userData.companyId}`);
            }

            // Mettre à jour l'état utilisateur avec toutes les informations
            setUser({
                ...decodedToken,
                ...userData,
                company: companyData
            });
        } catch (error) {
            console.error('Erreur lors du chargement des détails utilisateur:', error);
            logout();
        } finally {
            setIsLoading(false);
        }
    };

    useEffect(() => {
        // Vérifier si un token existe au chargement
        const token = localStorage.getItem('token');
        if (token) {
            loadUserDetails(token);
        } else {
            setIsLoading(false);
        }
        const handleForceLogout = () => logout();
        window.addEventListener('force-logout', handleForceLogout);
        return () => window.removeEventListener('force-logout', handleForceLogout);
    }, []);

    const login = async (token) => {
        localStorage.setItem('token', token);
        await loadUserDetails(token);
    };

    const logout = () => {
        localStorage.removeItem('token');
        setUser(null);
        delete apiClient.defaults.headers.common['Authorization'];
        navigate('/')
    };

    return (
        <AuthContext.Provider value={{
            user,
            login,
            logout,
            isLoading
        }}>
            {children}
        </AuthContext.Provider>
    );
};

export const useAuth = () => useContext(AuthContext);