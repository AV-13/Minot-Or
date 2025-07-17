// front/src/components/atoms/OrderStatus/OrderStatus.jsx
import React from 'react';
import styles from './OrderStatus.module.scss';

const OrderStatus = ({ status }) => (
    <span className={`${styles.status} ${styles[status.toLowerCase()]}`}>
        {status}
    </span>
);

export default OrderStatus;