import React from 'react';
import styles from './GenericRow.module.scss';

const GenericRow = ({ item, columns }) => (
    <tr className={styles.quotationItem}>
        {columns.map(col => (
            <td key={col.key}>
                {col.render ? col.render(item[col.key], item) : item[col.key]}
            </td>
        ))}
    </tr>
);

export default GenericRow;