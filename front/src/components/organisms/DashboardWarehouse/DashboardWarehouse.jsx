import React, { useEffect, useState } from 'react';
import AddWarehouseForm from '../../molecules/AddWarehouseForm/AddWarehouseForm';
import WarehouseTable from '../../molecules/WarehouseTable/WarehouseTable';
import apiClient from '../../../utils/apiClient';
import InputWithLabel from '../../molecules/InputWithLabel/InputWithLabel';

export default function DashboardWarehouse() {
    const [warehouses, setWarehouses] = useState([]);
    const [search, setSearch] = useState('');
    const [loading, setLoading] = useState(true);
    const [showForm, setShowForm] = useState(false);

    useEffect(() => { fetchWarehouses(); }, []);

    const fetchWarehouses = async () => {
        setLoading(true);
        try {
            const res = await apiClient.get('/warehouses');
            setWarehouses(Array.isArray(res.items) ? res.items : []);
        } catch (e) {
            setWarehouses([]);
        }
        setLoading(false);
    };

    const handleAdd = async (warehouse) => {
        await apiClient.post('/warehouses', warehouse);
        fetchWarehouses();
        setShowForm(false);
    };

    const handleDelete = async (id) => {
        await apiClient.delete(`/warehouses/${id}`);
        setWarehouses(warehouses.filter(w => w.id !== id));
    };
    console.log(warehouses);
    const filteredWarehouses = warehouses.filter(w =>
        w.storageCapacity.toString().toLowerCase().includes(search.toLowerCase()) ||
        w.warehouseAddress.toLowerCase().includes(search.toLowerCase())
    );

    return (
        <div>
            <h2>Dashboard Entrepôts</h2>
            <button onClick={() => setShowForm(v => !v)}>
                {showForm ? 'Annuler' : 'Ajouter un entrepôt'}
            </button>
            {showForm && <AddWarehouseForm onAdd={handleAdd} />}
            <InputWithLabel
                type="text"
                placeholder="Recherche par nom ou localisation"
                value={search}
                onChange={e => setSearch(e.target.value)}
            />
            {loading ? <p>Chargement...</p> :
                <WarehouseTable warehouses={filteredWarehouses} onDelete={handleDelete} />
            }
        </div>
    );
}