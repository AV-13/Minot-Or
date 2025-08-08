// front/src/components/pages/QuotationManagement/QuotationManagement.jsx
import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router';
import MainLayout from '../../templates/MainLayout';
import QuotationFilters from '../../organisms/QuotationFilters/QuotationFilters';
import QuotationTable from '../../organisms/QuotationTable/QuotationTable';
import apiClient from '../../../utils/apiClient';
import styles from './QuotationManagement.module.scss';

const QuotationManagement = () => {
    const navigate = useNavigate();
    const [quotations, setQuotations] = useState([]);
    const [loading, setLoading] = useState(true);
    const [page, setPage] = useState(1);
    const [limit] = useState(10);
    const [total, setTotal] = useState(0);
    const [searchTerm, setSearchTerm] = useState('');
    const [filters, setFilters] = useState({
        status: 'all',
        dateRange: { from: '', to: '' }
    });

    const fetchQuotations = async () => {
        try {
            setLoading(true);

            // Préparation des paramètres de requête
            const params = {
                page,
                limit,
                term: searchTerm || undefined
            };

            // Ajout des filtres de statut et de date s'ils sont définis
            if (filters.status && filters.status !== 'all') {
                params.status = filters.status;
            }

            if (filters.paymentStatus !== undefined) {
                params.paymentStatus = filters.paymentStatus;
            }

            if (filters.dateRange.from) {
                params.dateFrom = filters.dateRange.from;
            }

            if (filters.dateRange.to) {
                params.dateTo = filters.dateRange.to;
            }

            // Appel à l'API avec les paramètres
            const response = await apiClient.get('/quotations/admin', { params });

            setQuotations(response.items || []);
            setTotal(response.total || 0);
        } catch (error) {
            console.error('Erreur lors de la récupération des devis:', error);
        } finally {
            setLoading(false);
        }
    };

    // Appeler fetchQuotations à chaque changement de page, terme de recherche ou filtres
    useEffect(() => {
        fetchQuotations();
    }, [page, searchTerm, filters]);

    const handlePageChange = (newPage) => {
        setPage(newPage);
    };

    const handleSearch = (term) => {
        setSearchTerm(term);
        setPage(1); // Réinitialiser à la première page lors d'une nouvelle recherche
    };

    const handleFilterChange = (newFilters) => {
        setFilters(newFilters);
        setPage(1); // Réinitialiser à la première page lors d'un changement de filtre
    };

    const handleViewDetails = (quotationId) => {
        navigate(`/quotation/detail/${quotationId}`);
    };

    const handleMarkAsPaid = async (quotationId) => {
        try {
            await apiClient.patch(`/quotations/${quotationId}/pay`, {
                status: 'paid', // Assurez-vous que l'API remplace l'ancien statut
                payment_status: 1
            });
            // Rafraîchir la liste après le paiement
            fetchQuotations();
        } catch (error) {
            console.error('Erreur lors du marquage du devis comme payé:', error);
        }
    };

    return (
            <div className={styles.container}>
                <div className={styles.header}>
                    <h1>Gestion des Devis</h1>
                    <p>Consultez et gérez tous les devis clients</p>
                </div>

                <QuotationFilters
                    onSearch={handleSearch}
                    onFilterChange={handleFilterChange}
                />

                {loading ? (
                    <div className={styles.loading}>Chargement des devis...</div>
                ) : (
                    <QuotationTable
                        quotations={quotations}
                        currentPage={page}
                        totalPages={Math.ceil(total / limit)}
                        onPageChange={handlePageChange}
                        onViewDetails={handleViewDetails}
                        onMarkAsPaid={handleMarkAsPaid}
                    />
                )}
            </div>
    );
};

export default QuotationManagement;