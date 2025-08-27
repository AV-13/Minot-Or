// front/src/components/atoms/Toast/Toast.jsx
import React from 'react';
import styles from './Toast.module.scss';

const Toast = ({ message, type = 'info' }) => {
    return (
        <div className={`${styles.toast} ${styles[type]}`}>
            <div className={styles.message}>{message}</div>
        </div>
    );
};

export default Toast;