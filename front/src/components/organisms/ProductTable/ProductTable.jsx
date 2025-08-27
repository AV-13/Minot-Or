// Dans ProductTable.jsx
import React from 'react';
import styles from './ProductTable.module.scss';

const ProductTable = ({ products, onDelete, onEdit }) => {
    return (
        <table className={styles.table}>
            <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Type</th>
                <th>Prix</th>
                {/* Autres colonnes */}
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            {products.map(product => (
                <tr key={product.id}>
                    <td>{product.id}</td>
                    <td>{product.name}</td>
                    <td>{product.type}</td>
                    <td>{product.price} €</td>
                    {/* Autres colonnes */}
                    <td className={styles.actions}>
                        <button
                            className={styles.editButton}
                            onClick={() => onEdit(product)}
                        >
                            Éditer
                        </button>
                        <button
                            className={styles.deleteButton}
                            onClick={() => onDelete(product.id)}
                        >
                            Supprimer
                        </button>
                    </td>
                </tr>
            ))}
            </tbody>
        </table>
    );
};

export default ProductTable;