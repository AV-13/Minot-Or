// front/src/components/molecules/OrderRow/OrderRow.jsx
import React from 'react';
import OrderStatus from '../../atoms/OrderStatus/OrderStatus';
import styles from './OrderRow.module.scss';

const OrderRow = ({ order }) => (
    <tr className={styles.row}>
        <td>{order.id}</td>
        <td>{order.date}</td>
        <td>{order.total} €</td>
        <td><OrderStatus status={order.status} /></td>
    </tr>
);

export default OrderRow;