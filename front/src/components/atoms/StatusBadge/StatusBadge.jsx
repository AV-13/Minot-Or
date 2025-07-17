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
            case 'accepted':
                badgeClass = styles.accepted;
                label = 'Accepté';
                break;
            case 'rejected':
                badgeClass = styles.rejected;
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