import { Navigate, useLocation } from 'react-router';
import { useAuth } from '../../contexts/AuthContext';

export const RequireAuth = ({ children, allowedRoles = [] }) => {
    const { user, isLoading } = useAuth();
    const location = useLocation();

    if (isLoading) {
        return <div>Chargement...</div>;
    }

    if (!user) {
        // Rediriger vers la page de connexion si non authentifié
        return <Navigate to="/login" state={{ from: location }} replace />;
    }

    // Vérifier si l'utilisateur a au moins un des rôles autorisés
    const hasRole =
        allowedRoles.length === 0 ||
        Object.values(user.roles).some(role =>
            allowedRoles
                .map(r => r.toLowerCase())
                .includes(role.replace(/^ROLE_/, '').toLowerCase())
        );

    console.log(Object.values(user.roles));

    // Si des rôles sont spécifiés et que l'utilisateur n'a pas le rôle requis
    if (!hasRole) {
        // Rediriger vers une page d'accès refusé ou la page d'accueil
        return <Navigate to="/unauthorized" replace />;
    }

    return children;
};

// Composant pour afficher conditionnellement des éléments selon le rôle
export const AuthorizedElement = ({ children, allowedRoles = [] }) => {
    const { user } = useAuth();

    if (!user) return null;

    if (
        allowedRoles.length === 0 ||
        Object.values(user.roles).some(role =>
            allowedRoles
                .map(r => r.toLowerCase())
                .includes(role.replace(/^ROLE_/, '').toLowerCase())
        )
    ) {
        return <>{children}</>;
    }

    return null;
};