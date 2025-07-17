// front/src/components/pages/OrderHistory/OrderHistory.jsx
import React, { useState, useEffect } from 'react';
import MainLayout from '../../templates/MainLayout';
import OrderTable from '../../organisms/OrderTable/OrderTable';
import apiClient from '../../../utils/apiClient';
import styles from './OrderHistory.module.scss';

const OrderHistory = () => {
    const [orders, setOrders] = useState([]);

    useEffect(() => {
        const fetchOrders = async () => {
            const response = await apiClient.get('/orders/user');
            setOrders(response.data);
        };
        fetchOrders();
    }, []);

    return (
        <MainLayout>
            <div className={styles.container}>
                <h1>Historique des commandes</h1>
                <OrderTable orders={orders} />
            </div>
        </MainLayout>
    );
};

export default OrderHistory;