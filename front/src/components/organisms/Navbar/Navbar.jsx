// src/components/organisms/Navbar.jsx
import { Link } from 'react-router';
import { useAuth } from '../../../contexts/AuthContext';
import { AuthorizedElement } from '../../auth/RequireAuth';
import styles from './Navbar.module.scss';
import Logo from "../../atoms/Logo/Logo";

const Navbar = () => {
    const { user, logout } = useAuth();

    return (
        <nav className={styles.navbar}>
            <Link to="/">
                <Logo customClass={"navLogo"} ></Logo>
            </Link>
            <ul className={styles.navLinks}>
                {user && (
                    <>
                        <li><Link to="/dashboard">Tableau de bord</Link></li>
                        <li><Link to="/product">Produits</Link></li>
                        <AuthorizedElement allowedRoles={['Sales']}>
                            <li><Link to="/admin">Administration</Link></li>
                        </AuthorizedElement>
                        <AuthorizedElement allowedRoles={['Sales', 'Procurement']}>
                            <li><Link to="/fournisseurs">Gestion fournisseurs</Link></li>
                        </AuthorizedElement>
                        <AuthorizedElement allowedRoles={['Driver']}>
                            <li><Link to="/livraisons">Livraisons</Link></li>
                        </AuthorizedElement>
                    </>
                )}
            </ul>
            <div className={styles.actions}>
                {!user ? (
                    <>
                        <Link to="/login">Connexion</Link>
                        <Link to="/register">Inscription</Link>
                    </>
                ) : (
                    <button onClick={logout}>Déconnexion</button>
                )}
            </div>
        </nav>
    );
};

export default Navbar;
