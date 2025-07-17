// front/src/components/molecules/QuotationListItem/QuotationListItem.jsx
import React from 'react';
import { Link } from 'react-router';
import StatusBadge from '../../atoms/StatusBadge/StatusBadge';
import DateDisplay from '../../atoms/DateDisplay/DateDisplay';
import PriceDisplay from '../../atoms/PriceDisplay/PriceDisplay';
import styles from './QuotationListItem.module.scss';

const QuotationListItem = ({ quotation, onViewDetails }) => {
    console.log(quotation);
    return (
        <tr className={styles.listItem}>
            <td className={styles.id}>#{quotation.id}</td>
            <td className={styles.client}>
                {quotation.client ?
                    `${quotation.client.firstName || ''} ${quotation.client.lastName || ''}`
                    : 'Client inconnu'}
            </td>
            <td className={styles.date}>
                <DateDisplay date={quotation.issueDate} />
            </td>
            <td className={styles.dueDate}>
                <DateDisplay date={quotation.dueDate} />
            </td>
            <td className={styles.amount}>
                <PriceDisplay amount={quotation.totalAmount} />
            </td>
            <td className={styles.status}>
                <StatusBadge status={quotation.salesListStatus} paymentStatus={quotation.paymentStatus} />
            </td>
            <td className={styles.actions}>
                <button onClick={() => onViewDetails(quotation.id)} className={styles.viewButton}>
                    Voir
                </button>
            </td>
        </tr>
    );
};

export default QuotationListItem;