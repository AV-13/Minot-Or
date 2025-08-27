import React, { useState } from 'react';
import { useNavigate } from 'react-router';
import OrderStatus from '../../atoms/OrderStatus/OrderStatus';
import Pagination from '../../molecules/Pagination/Pagination';
import styles from './OrderTable.module.scss';

const OrderTable = ({ orders = [] }) => {
    const [page, setPage] = useState(1);
    const [limit, setLimit] = useState(5);
    const navigate = useNavigate();

    if (!orders || orders.length === 0) {
        return <p>Aucune commande ou devis à afficher</p>;
    }

    // Pagination
    const startIndex = (page - 1) * limit;
    const endIndex = Math.min(startIndex + limit, orders.length);
    const displayedOrders = orders.slice(startIndex, endIndex);

    const handlePageChange = (newPage) => {
        setPage(newPage);
    };

    const formatDate = (dateString) => {
        if (!dateString) return "Non disponible";

        // Vérifier si c'est au format "d-m-Y H:i"
        if (dateString.includes('-') && dateString.includes(':')) {
            // Format "31-12-2023 14:30" -> conversion en format supporté par JS
            const [datePart, timePart] = dateString.split(' ');
            const [day, month, year] = datePart.split('-');
            return new Date(`${year}-${month}-${day}T${timePart}`).toLocaleDateString('fr-FR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                // hour: '2-digit',
                // minute: '2-digit'
            });
        }

        // Si c'est déjà au format "YYYY-MM-DD"
        return new Date(dateString).toLocaleDateString('fr-FR');
    };

    const handleViewDetails = (item) => {
        // Redirection selon le type d'élément (devis ou commande)
        const isQuotation = item.paymentStatus !== undefined;
        const path = isQuotation ? `/quotation/detail/${item.id}` : `/quotation/detail/${item.id}`;
        navigate(path);
    };

    return (
        <div className={styles.container}>
            <table className={styles.table}>
                <thead>
                <tr>
                    <th>Référence</th>
                    <th>Date</th>
                    <th>Détails</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                {displayedOrders.map((item) => {
                    const isQuotation = item.paymentStatus !== undefined;
                    const reference = isQuotation
                        ? `#DEV-${item.id.toString().padStart(4, '0')}`
                        : `#CMD-${item.id.toString().padStart(4, '0')}`;

                    return (
                        <tr key={item.id}>
                            <td>{reference}</td>
                            <td>
                                {isQuotation
                                    ? formatDate(item.issueDate)
                                    : formatDate(item.delivery?.date)
                                }
                            </td>
                            <td>
                                {isQuotation ? (
                                    <>
                                        <div><strong>Montant:</strong> {item.totalAmount} €</div>
                                        <div><strong>Échéance:</strong> {formatDate(item.dueDate)}</div>
                                    </>
                                ) : (
                                    <>
                                        <div>{item.delivery?.address || 'Adresse non spécifiée'}</div>
                                        {item.products && (
                                            <div>{item.products.length} produit(s)</div>
                                        )}
                                    </>
                                )}
                            </td>
                            <td>
                                {isQuotation ? (
                                    <OrderStatus status={item.paymentStatus} />
                                ) : (
                                    <OrderStatus status={item.status} />
                                )}
                            </td>
                            <td className={styles.actions}>
                                <button
                                    title="Voir les détails"
                                    onClick={() => handleViewDetails(item)}
                                >
                                    👁️
                                </button>
                            </td>
                        </tr>
                    );
                })}
                </tbody>
            </table>

            <div className={styles.pagination}>
                <Pagination
                    page={page}
                    limit={limit}
                    total={orders.length}
                    onPageChange={handlePageChange}
                />
            </div>
        </div>
    );
};

export default OrderTable;