// front/src/components/organisms/QuotationFilters/QuotationFilters.jsx
import React, { useState } from 'react';
import styles from './QuotationFilters.module.scss';

const QuotationFilters = ({ onSearch, onFilterChange }) => {
    const [searchTerm, setSearchTerm] = useState('');
    const [dateRange, setDateRange] = useState({ from: '', to: '' });
    const [statusFilter, setStatusFilter] = useState('all');

    const handleSearch = (e) => {
        e.preventDefault();
        onSearch(searchTerm);
    };

    const handleStatusChange = (e) => {
        const newStatus = e.target.value;
        setStatusFilter(newStatus);

        if (newStatus === 'paid') {
            onFilterChange({
                status: 'all',
                paymentStatus: 1,
                dateRange
            });
        } else {
            onFilterChange({
                status: 'all',
                paymentStatus: 0,
                dateRange
            });
        }
    };

    const handleDateChange = (field, value) => {
        const newDateRange = { ...dateRange, [field]: value };
        setDateRange(newDateRange);
        onFilterChange({ status: statusFilter, dateRange: newDateRange });
    };

    return (
        <div className={styles.filtersContainer}>
            <div className={styles.searchContainer}>
                <form onSubmit={handleSearch}>
                    <input
                        type="text"
                        placeholder="Rechercher un devis par client ou référence"
                        value={searchTerm}
                        onChange={(e) => setSearchTerm(e.target.value)}
                        className={styles.searchInput}
                    />
                    <button type="submit" className={styles.searchButton}>Rechercher</button>
                </form>
            </div>

            <div className={styles.filterControls}>
                <div className={styles.filterGroup}>
                    <label htmlFor="status-filter">Statut :</label>
                    <select
                        id="status-filter"
                        value={statusFilter}
                        onChange={handleStatusChange}
                        className={styles.selectFilter}
                    >
                        <option value="all">Tous les statuts</option>
                        <option value="pending">En attente</option>
                        <option value="paid">Payé</option>
                    </select>
                </div>

                <div className={styles.filterGroup}>
                    <label>Période :</label>
                    <div className={styles.dateInputs}>
                        <input
                            type="date"
                            value={dateRange.from}
                            onChange={(e) => handleDateChange('from', e.target.value)}
                            className={styles.dateInput}
                        />
                        <span>à</span>
                        <input
                            type="date"
                            value={dateRange.to}
                            onChange={(e) => handleDateChange('to', e.target.value)}
                            className={styles.dateInput}
                        />
                    </div>
                </div>
            </div>
        </div>
    );
};

export default QuotationFilters;