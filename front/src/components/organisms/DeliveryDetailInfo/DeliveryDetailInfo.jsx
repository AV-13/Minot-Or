// front/src/components/organisms/DeliveryDetailInfo/DeliveryDetailInfo.jsx
import React from 'react';
import styles from './DeliveryDetailInfo.module.scss';

const DeliveryDetailInfo = ({ delivery }) => {
    if (!delivery) return <div className={styles.loading}>Chargement des informations de livraison...</div>;

    return (
        <div className={styles.deliveryCard}>
            <div className={styles.sectionHeader}>
                <span className={styles.icon}>🚚</span>
                <h3>Informations de livraison</h3>
            </div>

            <div className={styles.deliveryInfo}>
                <div className={styles.infoItem}>
                    <span className={styles.label}>Date de livraison:</span>
                    <span className={styles.value}>{new Date(delivery.deliveryDate).toLocaleDateString()}</span>
                </div>
                <div className={styles.infoItem}>
                    <span className={styles.label}>Adresse:</span>
                    <span className={styles.value}>{delivery.deliveryAddress}</span>
                </div>
                <div className={styles.infoItem}>
                    <span className={styles.label}>Numéro de livraison:</span>
                    <span className={styles.value}>{delivery.deliveryNumber}</span>
                </div>
            </div>
        </div>
    );
};

export default DeliveryDetailInfo;