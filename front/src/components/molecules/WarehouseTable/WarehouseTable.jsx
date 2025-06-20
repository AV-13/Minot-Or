import React from 'react';

export default function WarehouseTable({ warehouses, onDelete }) {
    if (!warehouses.length) return <p>Aucun entrepôt trouvé.</p>;
    return (
        <table>
            <thead>
            <tr>
                <th>Localisation</th>
                <th>Capacité de stockage</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            {warehouses.map(w => (
                <tr key={w.id}>
                    <td>{w.warehouseAddress}</td>
                    <td>{w.storageCapacity}</td>
                    <td>
                        <button onClick={() => onDelete(w.id)}>Supprimer</button>
                    </td>
                </tr>
            ))}
            </tbody>
        </table>
    );
}