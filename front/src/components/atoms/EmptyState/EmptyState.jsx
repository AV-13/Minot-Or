// front/src/components/atoms/EmptyState/EmptyState.jsx
import React from 'react';
import styles from './EmptyState.module.scss';

const EmptyState = ({ message, icon }) => {
    return (
        <div className={styles.emptyState}>
            {icon && <img src={icon} alt="Empty state" className={styles.icon} />}
            <p className={styles.message}>{message}</p>
        </div>
    );
};

export default EmptyState;