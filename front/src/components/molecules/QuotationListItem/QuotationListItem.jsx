import React, { useState } from 'react';
import styles from './QuotationListItem.module.scss';
import Button from '../../atoms/Button/Button';
import { FaUser, FaEuroSign, FaCalendarAlt } from 'react-icons/fa';
import apiClient from '../../../utils/apiClient';

const QuotationListItem = ({ quotation, onViewDetails, onMarkAsPaid, onEditSuccess }) => {
    const [isEditing, setIsEditing] = useState(false);
    const [discount, setDiscount] = useState(quotation.globalDiscount);

    const formatDate = (dateString) =>
        dateString ? new Date(dateString).toLocaleDateString('fr-FR') : 'N/A';
    const formatAmount = (amount) =>
        new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(amount);

    const getStatusLabel = (status) => {
        console.log(status);
        return ({
            'pending': 'En attente de paiement',
            'preparing_products': 'Préparation des produits',
            'awaiting_delivery': 'En attente de livraison',
        }[status] || 'Inconnu');
    }

    const handleEditClick = () => setIsEditing(true);
    const handleCancel = () => {
        setDiscount(quotation.globalDiscountng_);
        setIsEditing(false);
    };

    const handleSave = async () => {
        try {
            await apiClient.put(`/salesLists/${quotation.salesListId}`, { globalDiscount: discount });
            setIsEditing(false);
            if (onEditSuccess) onEditSuccess();
        } catch (e) {
            // Gérer l'erreur si besoin
        }
    };

    return (
        <tr className={styles.quotationItem}>
            <td>#{quotation.id}</td>
            <td>
                <FaUser style={{ marginRight: 6, color: '#2980b9' }} />
                {quotation.client ? `${quotation.client.firstName} ${quotation.client.lastName}` : 'Client inconnu'}
            </td>
            <td>
                <FaCalendarAlt style={{ marginRight: 6, color: '#7f8c8d' }} />
                {formatDate(quotation.issueDate)}
            </td>
            <td>{formatDate(quotation.dueDate)}</td>
            <td>{formatAmount(quotation.totalAmount - (quotation.totalAmount * (quotation.globalDiscount || 0) / 100))}</td>
            <td>
                {isEditing ? (
                    <input
                        type="number"
                        min={0}
                        value={discount}
                        onChange={e => setDiscount(Number(e.target.value))}
                        style={{ width: 80 }}
                    />
                ) : (
                    `${quotation.globalDiscount}%`
                )}
            </td>
            <td className={styles.status}>
                {quotation.paymentStatus ? (
                    <span className={`${styles.statusBadge} ${styles.paid}`}>Payé</span>
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
                {!quotation.paymentStatus && (
                    !isEditing ? (
                        <Button onClick={handleEditClick} className={styles.editButton}>
                            Modifier
                        </Button>
                    ) : (
                        <>
                            <Button onClick={handleSave} className={styles.saveButton}>Valider</Button>
                            <Button onClick={handleCancel} className={styles.cancelButton}>Annuler</Button>
                        </>
                    )
                )}
            </td>
        </tr>
    );
};

export default QuotationListItem;