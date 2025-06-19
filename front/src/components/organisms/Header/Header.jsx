import React from 'react';
import Button from '../../atoms/Button/Button';
import styles from './Header.module.scss';
import buttonStyles from '../../atoms/Button/Button.module.scss';

const Header = () => (
    <header className={styles.header}>
        <div className={styles.headerBgContainer}>
        <div className={styles.headerContainer}>
            <h1 className={styles.headerTitle}>Simplifiez vos approvisionnements en minoterie</h1>
            <p className={styles.headerDescription}>Gérez vos commandes, suivez vos livraisons et optimisez votre chaîne d'approvisionnement en quelques clics.</p>
            <div className={styles.headerButtonsContainer}>
                <Button customClass={buttonStyles.headerButtonWhite}>
                    <img src="/icons/cart.svg" alt="Icone panier" />
                    <span>Commander maintenant</span>
                </Button>
                <Button customClass={buttonStyles.headerButtonColored}>
                    <img src="/icons/info.svg" alt="Icone panier"/>
                    <span>En savoir plus</span>
                </Button>
            </div>
        </div>
        </div>
    </header>
);

export default Header;
