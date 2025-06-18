import React from 'react';
import styles from './HomeCard.module.scss';
import buttonStyles from '../../atoms/Button/Button.module.scss';
import Button from "../../atoms/Button/Button";

const HomeCard = ({ logo, title, description, buttonLabel, onButtonClick }) => (
    <div className={styles['home-card']}>
            <div className={styles['home-card-logo']}>
                    {typeof logo === 'string' ? <img src={logo} alt="" /> : logo}
            </div>
            <h3 className={styles['home-card-title']}>{title}</h3>
            <p className={styles['home-card-description']}>{description}</p>
            <Button customClass={buttonStyles.buttonNoBg} onClick={onButtonClick}>
                    {buttonLabel}
                <img className={styles.arrowRight} src="/icons/arrow-right.svg" alt="Icone flèche" />
            </Button>
    </div>
);

export default HomeCard;
