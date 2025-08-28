import React from 'react';
import Pagination from '../../molecules/Pagination/Pagination';
import styles from './GenericTable.module.scss';

const GenericTable = ({
    columns,
    data,
    RowComponent,
    page,
    limit,
    total,
    onPageChange
}) => {
    if (!data || data.length === 0) {
        return <div className={styles.emptyTable}>Aucune donnée trouvée</div>;
    }

    return (
        <div className={styles.tableContainer}>
            <table className={styles.quotationsTable}>
                <thead>
                    <tr>
                        {columns.map(col => (
                            <th key={col.key}>{col.label}</th>
                        ))}
                    </tr>
                </thead>
                <tbody>
                    {data.map(item => (
                        <RowComponent key={item.id} item={item} columns={columns} />
                    ))}
                </tbody>
            </table>
            <Pagination
                page={page}
                limit={limit}
                total={total}
                onPageChange={onPageChange}
            />
        </div>
    );
};

export default GenericTable;