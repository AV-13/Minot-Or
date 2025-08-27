// front/src/components/molecules/OrderRow/OrderRow.jsx
import React from 'react';
import OrderStatus from '../../atoms/OrderStatus/OrderStatus';
import styles from './OrderRow.module.scss';

const OrderRow = ({ order }) => {
    return(
    <tr className={styles.row}>
        <td>{order.id}</td>
        <td>{order.issueDate}</td>
        <td>{order.totalAmount} €</td>
        <td><OrderStatus status={order.paymentStatus}/></td>
    </tr>
    )
};

export default OrderRow;