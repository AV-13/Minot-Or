import React, { useEffect, useState } from 'react';
import AddWarehouseForm from '../../molecules/AddWarehouseForm/AddWarehouseForm';
import GenericTable from '../../organisms/GenericTable/GenericTable';
import GenericRow from '../../molecules/GenericRow/GenericRow';
import apiClient from '../../../utils/apiClient';
import InputWithLabel from '../../molecules/InputWithLabel/InputWithLabel';
import Pagination from "../../molecules/Pagination/Pagination";

export default function DashboardWarehouse() {
    const [warehouses, setWarehouses] = useState([]);
    const [search, setSearch] = useState('');
    const [searchInput, setSearchInput] = useState('');
    const [loading, setLoading] = useState(true);
    const [showForm, setShowForm] = useState(false);
    const [page, setPage] = useState(1);
    const [limit] = useState(20);
    const [total, setTotal] = useState(0);

    useEffect(() => { fetchWarehouses(); }, [page, search]);

    const fetchWarehouses = async () => {
        setLoading(true);
        try {
            const res = await apiClient.get('/warehouses', {
                params: {
                    page,
                    limit,
                    search
                }
            });
            setWarehouses(Array.isArray(res.items) ? res.items : []);
            setTotal(res.total || 0);
        } catch (e) {
            setWarehouses([]);
            setTotal(0);
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
        fetchWarehouses();
    };

    const handleSearch = () => {
        setPage(1);
        setSearch(searchInput);
    };

    // Colonnes pour GenericTable
    const columns = [
        { key: 'id', label: 'Id' },
        { key: 'warehouseAddress', label: 'Localisation' },
        { key: 'storageCapacity', label: 'Capacité de stockage' },
        {
            key: 'actions',
            label: 'Actions',
            render: (_, item) => (
                <button onClick={() => handleDelete(item.id)}>
                    Supprimer
                </button>
            )
        }
    ];

    return (
        <div>
            <button onClick={() => setShowForm(v => !v)}>
                {showForm ? 'Annuler' : 'Ajouter un entrepôt'}
            </button>
            {showForm && <AddWarehouseForm onAdd={handleAdd} />}
            <div style={{ display: 'flex', gap: 8, alignItems: 'center' }}>
                <InputWithLabel
                    type="text"
                    placeholder="Recherche par nom ou localisation"
                    value={searchInput}
                    onChange={e => setSearchInput(e.target.value)}
                />
                <button onClick={handleSearch}>Rechercher</button>
            </div>
            {loading ? <p>Chargement...</p> :
                <GenericTable
                    columns={columns}
                    data={warehouses}
                    RowComponent={GenericRow}
                    page={page}
                    limit={limit}
                    total={total}
                    onPageChange={setPage}
                />
            }
        </div>
    );
}