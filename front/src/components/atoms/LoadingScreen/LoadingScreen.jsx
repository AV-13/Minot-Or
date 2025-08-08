import React from 'react';
import styles from './LoadingScreen.module.scss';

const LoadingScreen = () => (
    <div className={styles.loadingScreen}>
        <div className={styles.spinner}></div>
    </div>
);

export default LoadingScreen;