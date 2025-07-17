// front/src/components/pages/QuotationManagement/QuotationManagement.jsx
import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router';
import Header from '../../organisms/Header/Header';
import Footer from '../../organisms/Footer/Footer';
import MainLayout from '../../templates/MainLayout';
import QuotationFilters from '../../organisms/QuotationFilters/QuotationFilters';
import QuotationTable from '../../organisms/QuotationTable/QuotationTable';
import apiClient from '../../../utils/apiClient';
import styles from './QuotationManagement.module.scss';

const QuotationManagement = () => {
    const navigate = useNavigate();
    const [quotations, setQuotations] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);
    const [filters, setFilters] = useState({
        search: '',
        status: 'all',
        dateRange: { from: '', to: '' }
    });

    const fetchQuotations = async (page = 1, filters = {}) => {
        try {
            setLoading(true);
            const params = new URLSearchParams();
            params.append('page', page);
            params.append('limit', 10);

            if (filters.term) {
                params.append('term', filters.term);
            }

            if (filters.dateRange?.from) {
                params.append('dateFrom', filters.dateRange.from);
            }

            if (filters.dateRange?.to) {
                params.append('dateTo', filters.dateRange.to);
            }

            if (filters.status && filters.status !== 'all') {
                params.append('status', filters.status);
            }

            // Utiliser le nouvel endpoint d'administration
            const response = await apiClient.get(`/quotations/admin?${params.toString()}`);
            setQuotations(response.items);
            setTotalPages(Math.ceil(response.total / response.limit));
            setCurrentPage(response.page);
        } catch (error) {
            setError("Erreur lors du chargement des devis. Veuillez réessayer.");
            console.error("Erreur API:", error);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchQuotations(currentPage, filters);
    }, [currentPage]);

    const handlePageChange = (page) => {
        setCurrentPage(page);
    };

    const handleSearch = (searchTerm) => {
        const newFilters = { ...filters, search: searchTerm };
        setFilters(newFilters);
        setCurrentPage(1);
        fetchQuotations(1, newFilters);
    };

    const handleFilterChange = (filterParams) => {
        const newFilters = { ...filters, ...filterParams };
        setFilters(newFilters);
        setCurrentPage(1);
        fetchQuotations(1, newFilters);
    };

    const handleViewDetails = (id) => {
        navigate(`/admin/quotations/${id}`);
    };

    return (
        <MainLayout>
            <Header />

            <div className={styles.pageHeader}>
                <h1>Gestion des devis</h1>
                <p>Consultez et gérez tous les devis de vos clients</p>
            </div>

            <div className={styles.pageContent}>
                <QuotationFilters
                    onSearch={handleSearch}
                    onFilterChange={handleFilterChange}
                />

                {loading ? (
                    <div className={styles.loading}>Chargement des devis...</div>
                ) : error ? (
                    <div className={styles.error}>{error}</div>
                ) : (
                    <QuotationTable
                        quotations={quotations}
                        currentPage={currentPage}
                        totalPages={totalPages}
                        onPageChange={handlePageChange}
                        onViewDetails={handleViewDetails}
                    />
                )}
            </div>

            <Footer />
        </MainLayout>
    );
};

export default QuotationManagement;