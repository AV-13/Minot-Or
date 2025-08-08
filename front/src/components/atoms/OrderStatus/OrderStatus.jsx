// front/src/components/atoms/OrderStatus/OrderStatus.jsx
import React from 'react';
import styles from './OrderStatus.module.scss';

const OrderStatus = ({ status }) => {
    console.log('OrderStatus : ', status);
    const PaymentStatusFrench = (status) => {
        switch (status) {
            case false:
                return 'En attente de paiement';
            case true:
                return 'Payé';
            default:
                return status;
        }
    };
    return(
        <span className={`${styles.status} ${typeof status === 'string' ? styles[status.toLowerCase()] : ''}`}>
            {PaymentStatusFrench(status)}
        </span>
    )
};

export default OrderStatus;