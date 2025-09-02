import React from 'react';
import styles from './PageHeader.module.scss';

const PageHeader = ({ title, description, backgroundImage = '/backgrounds/topography.svg' }) => (
    <div className={styles.pageHeader}>
        <div className={styles.background} style={{ backgroundImage: `url(${backgroundImage})` }}>
            <div className={styles.content}>
                <h1>{title}</h1>
                {description && <p>{description}</p>}
            </div>
        </div>
    </div>
);

export default PageHeader;