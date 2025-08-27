import React from 'react';
import Button from "../../atoms/Button/Button";
import styles from './pagination.module.scss';

export default function Pagination({ page, limit, total, onPageChange }) {
    const totalPages = Math.ceil(total / limit);

    if (totalPages <= 1) return null;

    return (
        <div className={styles.paginationContainer}>
            <Button disabled={page === 1} onClick={() => onPageChange(page - 1)}>
                Précédent
            </Button>
            <span className={styles.pageInfo}>Page {page} / {totalPages}</span>
            <Button disabled={page === totalPages} onClick={() => onPageChange(page + 1)}>
                Suivant
            </Button>
        </div>
    );
}