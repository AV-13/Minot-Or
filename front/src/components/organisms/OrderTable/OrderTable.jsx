// front/src/components/organisms/OrderTable/OrderTable.jsx
import React from 'react';
import OrderRow from '../../molecules/OrderRow/OrderRow';
import styles from './OrderTable.module.scss';

const OrderTable = ({ orders }) => (
    <table className={styles.table}>
        <thead>
        <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Total</th>
            <th>Statut</th>
        </tr>
        </thead>
        <tbody>
        {orders.map(order => (
            <OrderRow key={order.id} order={order} />
        ))}
        </tbody>
    </table>
);

export default OrderTable;