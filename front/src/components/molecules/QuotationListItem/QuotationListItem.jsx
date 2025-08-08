// Modification à apporter à QuotationListItem.jsx
import React from 'react';
import styles from './QuotationListItem.module.scss';
import Button from '../../atoms/Button/Button';

const QuotationListItem = ({ quotation, onViewDetails, onMarkAsPaid }) => {
    const formatDate = (dateString) => {
        if (!dateString) return 'N/A';
        return new Date(dateString).toLocaleDateString('fr-FR');
    };

    const formatAmount = (amount) => {
        return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(amount);
    };

    const getStatusLabel = (status) => {
        const statusMap = {
            'pending': 'En attente',
            'accepted': 'Accepté',
            'rejected': 'Refusé',
            'paid': 'Payé'
        };
        return statusMap[status] || 'Inconnu';
    };

    return (
        <tr className={styles.quotationItem}>
            <td>#{quotation.id}</td>
            <td>{quotation.client ? `${quotation.client.firstName} ${quotation.client.lastName}` : 'Client inconnu'}</td>
            <td>{formatDate(quotation.issueDate)}</td>
            <td>{formatDate(quotation.dueDate)}</td>
            <td>{formatAmount(quotation.totalAmount)}</td>
            <td className={styles.status}>
                {quotation.paymentStatus ? (
                    <span className={`${styles.statusBadge} ${styles.paid}`}>
            Payé
        </span>
                ) : (
                    <span className={`${styles.statusBadge} ${styles[quotation.salesListStatus || 'pending']}`}>
            {getStatusLabel(quotation.salesListStatus)}
        </span>
                )}
            </td>
            <td className={styles.actions}>
                <Button onClick={() => onViewDetails(quotation.id)}>
                    Détails
                </Button>
                {!quotation.paymentStatus && (
                    <Button
                        onClick={() => onMarkAsPaid(quotation.id)}
                        className={styles.payButton}
                    >
                        Marquer comme payé
                    </Button>
                )}
            </td>
        </tr>
    );
};

export default QuotationListItem;