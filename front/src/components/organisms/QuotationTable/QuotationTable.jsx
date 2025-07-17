// front/src/components/organisms/QuotationTable/QuotationTable.jsx
import React from 'react';
import QuotationListItem from '../../molecules/QuotationListItem/QuotationListItem';
import Pagination from '../../molecules/Pagination/Pagination';
import styles from './QuotationTable.module.scss';

const QuotationTable = ({ quotations, currentPage, totalPages, onPageChange, onViewDetails }) => {
    if (!quotations || quotations.length === 0) {
        return <div className={styles.emptyTable}>Aucun devis trouvé</div>;
    }

    return (
        <div className={styles.tableContainer}>
            <table className={styles.quotationsTable}>
                <thead>
                <tr>
                    <th>Référence</th>
                    <th>Client</th>
                    <th>Date d'émission</th>
                    <th>Date d'échéance</th>
                    <th>Montant total</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                {quotations.map(quotation => (
                    <QuotationListItem
                        key={quotation.id}
                        quotation={quotation}
                        onViewDetails={onViewDetails}
                    />
                ))}
                </tbody>
            </table>

            <Pagination
                currentPage={currentPage}
                totalPages={totalPages}
                onPageChange={onPageChange}
            />
        </div>
    );
};

export default QuotationTable;