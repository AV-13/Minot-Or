import React from 'react';
import styles from './CatchyFooter.module.scss';
import buttonStyles from '../../atoms/Button/Button.module.scss';
import Button from "../../atoms/Button/Button";

const CatchyFooter = () => (
    <footer className={styles.catchyFooter}>
        <div className={styles.background}>
        <div className={styles.content}>
            <h2>Prêt à simplifier vos approvisionnements ?</h2>
            <p>Rejoignez les nombreux professionnels qui font confiance à Minot'Or pour optimiser leur chaîne d'approvisionnement.</p>
            <div className={styles.buttonsContainer}>
                <Button customClass={buttonStyles.headerButtonWhite}>
                    <span>Rejoindre la plateforme</span>
                </Button>
                <Button customClass={buttonStyles.headerButtonColored}>
                    <img src="/icons/info.svg" alt="Icone panier"/>
                    <span>Nous contacter</span>
                </Button>
            </div>
        </div>
    </div>
    </footer>
);

export default CatchyFooter;
