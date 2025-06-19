import React, { useState } from 'react';
import Input from "../../atoms/Input/Input";
import Button from "../../atoms/Button/Button";

export default function AddWarehouseForm({ onAdd }) {
    const [storageCapacity, setStorageCapacity] = useState();
    const [warehouseAddress, setwarehouseAddress] = useState('');

    const handleSubmit = (e) => {
        e.preventDefault();
        if (!storageCapacity || !warehouseAddress) return;
        console.log({ storageCapacity, warehouseAddress })
        onAdd({ storageCapacity, warehouseAddress: warehouseAddress });
        setStorageCapacity();
        setwarehouseAddress('');
    };

    return (
        <form onSubmit={handleSubmit} style={{ display: 'flex', gap: 8, marginBottom: 16, flexWrap: 'wrap' }}>
            <Input placeholder="Capacité de stockage" value={storageCapacity} onChange={e => setStorageCapacity(e.target.value)} required />
            <Input placeholder="Localisation" value={warehouseAddress} onChange={e => setwarehouseAddress(e.target.value)} required />
            <Button type="submit">Ajouter</Button>
        </form>
    );
}