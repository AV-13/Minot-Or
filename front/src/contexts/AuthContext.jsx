import { createContext, useState, useEffect, useContext } from 'react';
import { jwtDecode } from 'jwt-decode';
import apiClient from '../utils/apiClient';

const AuthContext = createContext();

export const AuthProvider = ({ children }) => {
    const [user, setUser] = useState(null);
    const [isLoading, setIsLoading] = useState(true);

    useEffect(() => {
        // Vérifier si un token existe au chargement
        const token = localStorage.getItem('token');
        if (token) {
            try {
                const decodedToken = jwtDecode(token);
                setUser(decodedToken);
                apiClient.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            } catch (error) {
                localStorage.removeItem('token');
            }
        }
        setIsLoading(false);
    }, []);

    const login = (token) => {
        localStorage.setItem('token', token);
        const decodedToken = jwtDecode(token);
        setUser(decodedToken);
        apiClient.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    };

    const logout = () => {
        localStorage.removeItem('token');
        setUser(null);
        delete apiClient.defaults.headers.common['Authorization'];
    };

    return (
        <AuthContext.Provider value={{ user, login, logout, isLoading }}>
            {children}
        </AuthContext.Provider>
    );
};

export const useAuth = () => useContext(AuthContext);