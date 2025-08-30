import React from 'react';
import styles from './StatusBadge.module.scss';

const StatusBadge = ({ status, paymentStatus }) => {
    let badgeClass;
    let label;

    if (paymentStatus) {
        badgeClass = styles.paid;
        label = 'Payé';
    } else {
        switch (status) {
            case 'pending':
                badgeClass = styles.pending;
                label = 'En attente';
                break;
            case 'preparing_products':
                badgeClass = styles.pending;
                label = 'Accepté';
                break;
            case 'awaiting_delivery':
                badgeClass = styles.pending;
                label = 'Refusé';
                break;
            default:
                badgeClass = styles.default;
                label = status;
        }
    }

    return <span className={`${styles.badge} ${badgeClass}`}>{label}</span>;
};

export default StatusBadge;