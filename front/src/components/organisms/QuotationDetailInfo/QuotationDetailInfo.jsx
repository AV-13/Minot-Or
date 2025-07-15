// front/src/components/organisms/QuotationDetailInfo/QuotationDetailInfo.jsx
import React from 'react';
import styles from './QuotationDetailInfo.module.scss';

const QuotationDetailInfo = ({ quotation }) => {
    if (!quotation) return <div className={styles.loading}>Chargement des informations...</div>;

    return (
        <div className={styles.detailCard}>
            <div className={styles.sectionHeader}>
                <span className={styles.icon}>📋</span>
                <h3>Détails du devis #{quotation.id}</h3>
            </div>

            <div className={styles.infoGrid}>
                <div className={styles.infoItem}>
                    <span className={styles.label}>Date de création:</span>
                    <span className={styles.value}>{new Date(quotation.issueDate).toLocaleDateString()}</span>
                </div>
                <div className={styles.infoItem}>
                    <span className={styles.label}>Date d'expiration:</span>
                    <span className={styles.value}>{new Date(quotation.expirationDate).toLocaleDateString()}</span>
                </div>
                <div className={styles.infoItem}>
                    <span className={styles.label}>Statut:</span>
                    <span className={`${styles.value} ${styles.status}`}>{quotation.status}</span>
                </div>
            </div>
        </div>
    );
};

export default QuotationDetailInfo;