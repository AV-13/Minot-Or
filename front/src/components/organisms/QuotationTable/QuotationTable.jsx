// Modification à apporter à QuotationTable.jsx
import React from 'react';
import QuotationListItem from '../../molecules/QuotationListItem/QuotationListItem';
import Pagination from '../../molecules/Pagination/Pagination';
import styles from './QuotationTable.module.scss';

const QuotationTable = ({
                            quotations,
                            currentPage,
                            totalPages,
                            onPageChange,
                            onViewDetails,
                            onMarkAsPaid,
                            fetchQuotations
                        }) => {
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
                    <th>Remise globale</th>
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
                        onMarkAsPaid={onMarkAsPaid}
                        onEditSuccess={fetchQuotations}
                    />
                ))}
                </tbody>
            </table>

            <Pagination
                page={currentPage}
                limit={quotations.length}
                total={totalPages * quotations.length}
                onPageChange={onPageChange}
            />
        </div>
    );
};

export default QuotationTable;