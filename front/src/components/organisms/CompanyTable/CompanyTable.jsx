// front/src/components/organisms/CompanyTable/CompanyTable.jsx
import React from 'react';
import styles from './CompanyTable.module.scss';
import StatusBadge from '../../atoms/StatusBadge/StatusBadge';

const CompanyTable = ({ companies, onMarkRecovered }) => {
    if (!companies || companies.length === 0) {
        return <p>Aucune entreprise trouvée.</p>;
    }

    return (
        <div className={styles.tableContainer}>
            <table className={styles.table}>
                <thead>
                <tr>
                    <th>Nom</th>
                    <th>SIRET</th>
                    <th>Contact</th>
                    <th>Invendus</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                {companies.map((company) => (
                    <tr key={company.id}>
                        <td>{company.companyName}</td>
                        <td>{company.companySiret}</td>
                        <td>{company.companyContact}</td>
                        <td>
                            {company.unsold ? (
                                <StatusBadge status="warning" text="Invendus disponibles" />
                            ) : (
                                <StatusBadge status="success" text="Aucun invendu" />
                            )}
                        </td>
                        <td>
                            {company.unsold && (
                                <button
                                    className={styles.actionButton}
                                    onClick={() => onMarkRecovered(company.id)}
                                >
                                    Marquer comme récupérés
                                </button>
                            )}
                        </td>
                    </tr>
                ))}
                </tbody>
            </table>
        </div>
    );
};

export default CompanyTable;