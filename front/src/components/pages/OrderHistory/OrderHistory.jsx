import React, { useState, useEffect } from 'react';
import MainLayout from '../../templates/MainLayout';
import OrderTable from '../../organisms/OrderTable/OrderTable';
import PageHeader from "../../molecules/PageHeader/PageHeader";
import apiClient from '../../../utils/apiClient';
import styles from './OrderHistory.module.scss';

const OrderHistory = () => {
    const [orders, setOrders] = useState([]);
    const [userId, setUserId] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        const fetchData = async () => {
            try {
                setLoading(true);

                // Récupérer l'utilisateur courant
                const userResponse = await apiClient.get('/users/me');
                const currentUserId = userResponse.id;
                setUserId(currentUserId);

                // Récupérer les commandes passées de l'utilisateur
                const ordersResponse = await apiClient.get(`/salesLists/user/${currentUserId}`);
                console.log("Commandes récupérées :", ordersResponse);
                setOrders(ordersResponse);

            } catch (error) {
                console.error("Erreur lors de la récupération des données :", error);
            } finally {
                setLoading(false);
            }
        };

        fetchData();
    }, []);

    return (
        <MainLayout>
            <PageHeader
                title="Historique des commandes"
                description="Consultez toutes vos commandes passées et leur statut"
            />
            <div className={styles.container}>
                {loading ? (
                    <p>Chargement...</p>
                ) : error ? (
                    <p className={styles.error}>{error}</p>
                ) : (
                    <OrderTable orders={orders} />
                )}
            </div>
        </MainLayout>
    );
};

export default OrderHistory;