// front/src/components/organisms/Navbar/Navbar.jsx
import React from 'react';
import { NavLink } from 'react-router';
import { useAuth } from '../../../contexts/AuthContext';
import { AuthorizedElement } from '../../auth/RequireAuth';
import styles from './Navbar.module.scss';
import Logo from "../../atoms/Logo/Logo";
import ProfileAvatar from "../../atoms/ProfileAvatar/ProfileAvatar";
import DropdownMenu from "../../atoms/DropdownMenu/DropdownMenu";

const Navbar = () => {
    const { user, logout } = useAuth();

    // Suppression de "Vue générale" du menu déroulant
    const dashboardItems = [
        { label: 'Utilisateurs', path: '/dashboard/users' },
        { label: 'Produits', path: '/dashboard/products' },
        { label: 'Entrepôts', path: '/dashboard/warehouses' },
        { label: 'Devis', path: '/dashboard/quotations' },
    ];

    return (
        <nav className={styles.navbar}>
            <NavLink to="/">
                <Logo customClass={"navLogo"} />
            </NavLink>
            <ul className={styles.navLinks}>
                {user && (
                    <>
                        <li>
                            <NavLink to="/" end className={({isActive}) => isActive ? styles.active : undefined}>
                                Accueil
                            </NavLink>
                        </li>
                        <AuthorizedElement allowedRoles={['sales']}>
                            <li className={styles.dropdownContainer}>
                                    <DropdownMenu
                                        trigger="Tableau de bord"
                                        items={dashboardItems}
                                        mainLink="/dashboard"
                                    />
                            </li>
                        </AuthorizedElement>
                        <li>
                            <NavLink to="/product" className={({isActive}) => isActive ? styles.active : undefined}>
                                Produits
                            </NavLink>
                        </li>
                        <li>
                            <NavLink to="/order-history" className={({isActive}) => isActive ? styles.active : undefined}>
                                Mes commandes
                            </NavLink>
                        </li>
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
                    </>
                )}
            </div>
        </nav>
    );
};

export default Navbar;