// src/components/organisms/Navbar.jsx
import {Link, NavLink} from 'react-router';
import { useAuth } from '../../../contexts/AuthContext';
import { AuthorizedElement } from '../../auth/RequireAuth';
import styles from './Navbar.module.scss';
import Logo from "../../atoms/Logo/Logo";
import ProfileAvatar from "../../atoms/ProfileAvatar/ProfileAvatar";

const Navbar = () => {
    const { user, logout } = useAuth();
    return (
        <nav className={styles.navbar}>
            <NavLink to="/">
                <Logo customClass={"navLogo"} ></Logo>
            </NavLink>
            <ul className={styles.navLinks}>
                {user && (
                    <>
                        <li>
                            <NavLink to="/" end className={({isActive}) => isActive ? styles.active : undefined}>
                                Accueil
                            </NavLink>
                        </li>
                        <li><NavLink to="/dashboard" className={({isActive}) => isActive ? styles.active : undefined}>Tableau
                            de bord</NavLink></li>
                        <li><NavLink to="/product"
                                     className={({isActive}) => isActive ? styles.active : undefined}>Produits</NavLink>
                        </li>
                        <AuthorizedElement allowedRoles={['Sales']}>
                            <li><NavLink to="/admin"
                                         className={({isActive}) => isActive ? styles.active : undefined}>Administration</NavLink>
                            </li>
                        </AuthorizedElement>
                        <AuthorizedElement allowedRoles={['Sales', 'Procurement']}>
                            <li><NavLink to="/fournisseurs"
                                         className={({isActive}) => isActive ? styles.active : undefined}>Gestion
                                fournisseurs</NavLink></li>
                        </AuthorizedElement>
                        <AuthorizedElement allowedRoles={['Driver']}>
                            <li><NavLink to="/livraisons"
                                         className={({isActive}) => isActive ? styles.active : undefined}>Livraisons</NavLink>
                            </li>
                        </AuthorizedElement>
                    </>
                )}
            </ul>
            <div className={styles.actions}>
                {!user ? (
                    <>
                        <NavLink to="/login">Connexion</NavLink>
                        <NavLink to="/register">Inscription</NavLink>
                    </>
                ) : (
                    <>
                        <ProfileAvatar userName={user?.username} />
                        <img
                            src="/icons/power-off.svg"
                            alt="Déconnexion"
                            className={styles.logoutIcon}
                            onClick={logout}
                            style={{cursor: 'pointer', width: 22, height: 22}}
                        />
                        {/*<button onClick={logout}>Déconnexion</button>*/}
                    </>
                )}
            </div>
        </nav>
    );
};

export default Navbar;
