import React from 'react';
import { Link } from 'react-router';
import styles from './DashboardCard.module.scss';

const DashboardCard = ({ title, description, icon, link }) => {
    return (
        <Link to={link} className={styles.card}>
            <div className={styles.iconContainer}>
                <img src={icon} alt={title} className={styles.icon} />
            </div>
            <h3 className={styles.title}>{title}</h3>
            <p className={styles.description}>{description}</p>
        </Link>
    );
};

export default DashboardCard;