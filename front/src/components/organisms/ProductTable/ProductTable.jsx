import React from 'react';

export default function ProductTable({ products, onDelete }) {
    if (!products.length) return <p>Aucun produit trouvé.</p>;
    return (
        <table>
            <thead>
            <tr>
                <th>Nom</th>
                <th>Catégorie</th>
                <th>Prix Net</th>
                <th>Prix Brut</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            {products.map(p => (
                <tr key={p.id}>
                    <td>{p.name}</td>
                    <td>{p.category}</td>
                    <td>{p.netPrice}</td>
                    <td>{p.grossPrice}</td>
                    <td>{p.stockQuantity}</td>
                    <td>
                        <button onClick={() => onDelete(p.id)}>Supprimer</button>
                    </td>
                </tr>
            ))}
            </tbody>
        </table>
    );
}