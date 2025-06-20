import React from 'react';
import Button from "../../atoms/Button/Button";

export default function Pagination({ page, limit, total, onPageChange }) {
    const totalPages = Math.ceil(total / limit);

    if (totalPages <= 1) return null;

    return (
        <div style={{ display: 'flex', alignItems: 'center', gap: 8, marginTop: 16 }}>
            <Button disabled={page === 1} onClick={() => onPageChange(page - 1)}>
                Précédent
            </Button>
            <span>Page {page} / {totalPages}</span>
            <Button disabled={page === totalPages} onClick={() => onPageChange(page + 1)}>
                Suivant
            </Button>
        </div>
    );
}