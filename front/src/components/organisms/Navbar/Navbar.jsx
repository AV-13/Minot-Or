import React, { useState, useEffect } from 'react';
import { NavLink } from 'react-router';
import { useAuth } from '../../../contexts/AuthContext';
import { AuthorizedElement } from '../../auth/RequireAuth';
import styles from './Navbar.module.scss';
import Logo from "../../atoms/Logo/Logo";
import ProfileAvatar from "../../atoms/ProfileAvatar/ProfileAvatar";
import DropdownMenu from "../../atoms/DropdownMenu/DropdownMenu";

const Navbar = () => {
    const { user, logout } = useAuth();
    const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
    const [isMobileDashboardOpen, setIsMobileDashboardOpen] = useState(false);

    const dashboardItems = [
        { label: 'Utilisateurs', path: '/dashboard/users' },
        { label: 'Produits', path: '/dashboard/products' },
        { label: 'Entrepôts', path: '/dashboard/warehouses' },
        { label: 'Devis', path: '/dashboard/quotations' },
    ];

    const toggleMobileMenu = () => {
        setIsMobileMenuOpen(!isMobileMenuOpen);
        setIsMobileDashboardOpen(false); // Ferme le dropdown quand on ouvre/ferme le menu
    };

    const closeMobileMenu = () => {
        setIsMobileMenuOpen(false);
        setIsMobileDashboardOpen(false);
    };

    const toggleMobileDashboard = () => {
        setIsMobileDashboardOpen(!isMobileDashboardOpen);
    };

    const handleLogout = () => {
        logout();
        closeMobileMenu();
    };

    // Ferme le menu mobile lors du redimensionnement
    useEffect(() => {
        const handleResize = () => {
            if (window.innerWidth > 768) {
                setIsMobileMenuOpen(false);
                setIsMobileDashboardOpen(false);
            }
        };

        window.addEventListener('resize', handleResize);
        return () => window.removeEventListener('resize', handleResize);
    }, []);

    // Bloque le scroll du body quand le menu mobile est ouvert
    useEffect(() => {
        if (isMobileMenuOpen) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = 'unset';
        }

        return () => {
            document.body.style.overflow = 'unset';
        };
    }, [isMobileMenuOpen]);

    return (
        <>
            <nav className={styles.navbar}>
                <NavLink to="/">
                    <Logo customClass={"navLogo"} />
                </NavLink>

                {/* Menu desktop */}
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

                {/* Actions desktop */}
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
                                onClick={handleLogout}
                                style={{cursor: 'pointer', width: 22, height: 22}}
                            />
                        </>
                    )}
                </div>

                {/* Menu burger */}
                <div
                    className={`${styles.burgerMenu} ${isMobileMenuOpen ? styles.active : ''}`}
                    onClick={toggleMobileMenu}
                >
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </nav>

            {/* Overlay mobile */}
            <div
                className={`${styles.mobileOverlay} ${isMobileMenuOpen ? styles.active : ''}`}
                onClick={closeMobileMenu}
            />

            {/* Menu mobile */}
            <div className={`${styles.mobileMenu} ${isMobileMenuOpen ? styles.active : ''}`}>
                {/* Bouton de fermeture */}
                <button
                    className={styles.closeButton}
                    onClick={closeMobileMenu}
                    aria-label="Fermer le menu"
                />

                <div className={styles.mobileMenuContent}>
                    {user && (
                        <div className={styles.mobileProfileSection}>
                            <ProfileAvatar userName={user?.username} />
                        </div>
                    )}

                    <ul className={styles.mobileNavLinks}>
                        {user ? (
                            <>
                                <li>
                                    <NavLink
                                        to="/"
                                        end
                                        className={({isActive}) => isActive ? styles.active : undefined}
                                        onClick={closeMobileMenu}
                                    >
                                        Accueil
                                    </NavLink>
                                </li>
                                <AuthorizedElement allowedRoles={['sales']}>
                                    <li className={styles.mobileDropdown}>
                                        <button
                                            className={`${styles.dropdownTrigger} ${isMobileDashboardOpen ? styles.active : ''}`}
                                            onClick={toggleMobileDashboard}
                                        >
                                            Tableau de bord
                                            <svg
                                                className={`${styles.chevron} ${isMobileDashboardOpen ? styles.rotated : ''}`}
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                strokeWidth="2"
                                            >
                                                <polyline points="6,9 12,15 18,9"></polyline>
                                            </svg>
                                        </button>
                                        <div className={`${styles.dropdownContent} ${isMobileDashboardOpen ? styles.open : ''}`}>
                                            {dashboardItems.map((item, index) => (
                                                <NavLink
                                                    key={index}
                                                    to={item.path}
                                                    className={({isActive}) => isActive ? styles.active : undefined}
                                                    onClick={closeMobileMenu}
                                                >
                                                    {item.label}
                                                </NavLink>
                                            ))}
                                        </div>
                                    </li>
                                </AuthorizedElement>
                                <li>
                                    <NavLink
                                        to="/product"
                                        className={({isActive}) => isActive ? styles.active : undefined}
                                        onClick={closeMobileMenu}
                                    >
                                        Produits
                                    </NavLink>
                                </li>
                                <li>
                                    <NavLink
                                        to="/order-history"
                                        className={({isActive}) => isActive ? styles.active : undefined}
                                        onClick={closeMobileMenu}
                                    >
                                        Mes commandes
                                    </NavLink>
                                </li>
                            </>
                        ) : (
                            <>
                                <li>
                                    <NavLink to="/login" onClick={closeMobileMenu}>
                                        Connexion
                                    </NavLink>
                                </li>
                                <li>
                                    <NavLink to="/register" onClick={closeMobileMenu}>
                                        Inscription
                                    </NavLink>
                                </li>
                            </>
                        )}
                    </ul>

                    {user && (
                        <div className={styles.mobileActions}>
                            <button className={styles.logout} onClick={handleLogout}>
                                <img
                                    src="/icons/power-off.svg"
                                    alt=""
                                    style={{width: 18, height: 18}}
                                />
                                Déconnexion
                            </button>
                        </div>
                    )}
                </div>
            </div>
        </>
    );
};

export default Navbar;