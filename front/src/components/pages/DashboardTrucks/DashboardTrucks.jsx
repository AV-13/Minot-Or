// front/src/components/pages/DashboardTrucks/DashboardTrucks.jsx
import React, { useEffect, useState } from 'react';
import MainLayout from '../../templates/MainLayout';
import GenericTable from '../../organisms/GenericTable/GenericTable';
import GenericRow from '../../molecules/GenericRow/GenericRow';
import apiClient from '../../../utils/apiClient';
import GenericFilters from "../../organisms/GenericFilters/GenericFilters";

const columns = [
    { key: 'registrationNumber', label: 'Immatriculation' },
    { key: 'truckType', label: 'Type' },
    { key: 'isAvailable', label: 'Disponible', render: (value) => value ? 'Oui' : 'Non' },
    { key: 'deliveryCount', label: 'Livraisons' },
    { key: 'transportDistance', label: 'Distance (km)' },
    { key: 'transportFee', label: 'Frais (€)' }
    // Ajoute d'autres colonnes si besoin
];

export default function DashboardTrucks() {
    const [trucks, setTrucks] = useState([]);
    const [search, setSearch] = useState('');
    const [page, setPage] = useState(1);
    const [limit] = useState(10);
    const [total, setTotal] = useState(0);

    useEffect(() => {
        console.log(search);
        apiClient.get('/trucks', { params: { page, limit, search } })
            .then(res => {
                setTrucks(res.items);
                setTotal(res.total);
            });
    }, [page, search]);

    const handleSearch = (term) => {
        setSearch(term.searchTerm);
        setPage(1);
    };

    const filtersConfig = [
        {
            type: 'search',
            name: 'searchTerm',
            placeholder: 'Rechercher un camion...'
        }
    ];

    return (
        <MainLayout>
            <h1>Gestion des camions</h1>

            <GenericFilters filtersConfig={filtersConfig} onSearch={handleSearch}/>

            <GenericTable
                columns={columns}
                data={trucks}
                RowComponent={GenericRow}
                page={page}
                limit={limit}
                total={total}
                onPageChange={setPage}
            />
        </MainLayout>
    );
}