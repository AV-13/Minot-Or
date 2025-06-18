// src/components/organisms/Navbar.jsx
import { Link } from 'react-router';
import { useAuth } from '../../contexts/AuthContext';
import { AuthorizedElement } from '../auth/RequireAuth';

const Navbar = () => {
    const { user, logout } = useAuth();

    return (
        <nav>
            <ul>
                <li><Link to="/">Accueil</Link></li>

                {!user ? (
                    <>
                        <li><Link to="/login">Connexion</Link></li>
                        <li><Link to="/register">Inscription</Link></li>
                    </>
                ) : (
                    <>
                        <li><Link to="/dashboard">Tableau de bord</Link></li>
                        <li><Link to="/product">Produits</Link></li>

                        {/* Éléments visibles uniquement par certains rôles */}
                        <AuthorizedElement allowedRoles={['Sales']}>
                            <li><Link to="/admin">Administration</Link></li>
                        </AuthorizedElement>

                        <AuthorizedElement allowedRoles={['Sales', 'Procurement']}>
                            <li><Link to="/fournisseurs">Gestion fournisseurs</Link></li>
                        </AuthorizedElement>

                        <AuthorizedElement allowedRoles={['Driver']}>
                            <li><Link to="/livraisons">Livraisons</Link></li>
                        </AuthorizedElement>

                        <li><button onClick={logout}>Déconnexion</button></li>
                    </>
                )}
            </ul>
        </nav>
    );
};

export default Navbar;