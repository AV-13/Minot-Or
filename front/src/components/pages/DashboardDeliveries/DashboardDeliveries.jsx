import React, { useEffect, useState } from 'react';
import MainLayout from '../../templates/MainLayout';
import GenericTable from '../../organisms/GenericTable/GenericTable';
import GenericRow from '../../molecules/GenericRow/GenericRow';
import QrModal from "../../atoms/QrModal/QrModal";
import Button from "../../atoms/Button/Button";
import apiClient from '../../../utils/apiClient';
import GenericFilters from "../../organisms/GenericFilters/GenericFilters";

export default function DashboardDeliveries() {
    const [qrModalOpen, setQrModalOpen] = useState(false);
    const [selectedQr, setSelectedQr] = useState('');
    const [deliveries, setDeliveries] = useState([]);
    const [page, setPage] = useState(1);
    const [search, setSearch] = useState('');
    const [limit] = useState(10);
    const [total, setTotal] = useState(0);

    const columns = [
    { key: 'deliveryNumber', label: 'Numéro' },
    { key: 'deliveryDate', label: 'Date', render: (value) => new Date(value).toLocaleDateString() },
    { key: 'deliveryStatus', label: 'Statut', render: (value) => {
        switch (value) {
            case 'in_preparation': return 'En préparation';
            case 'delivered': return 'Livrée';
            case 'cancelled': return 'Annulée';
            default: return value;
        }
    }},
    { key: 'deliveryAddress', label: 'Adresse' },
    { key: 'driverRemark', label: 'Remarque chauffeur' },
    {
        key: 'qrCode',
        label: 'QR Code',
        render: (value) => (
            <Button onClick={() => { setSelectedQr(value); setQrModalOpen(true); }}>
                Voir le QR
            </Button>
        )
    }
];

    useEffect(() => {
    apiClient.get('/deliveries', { params: { page, limit } })
        .then(res => {
            setDeliveries(res.items);
            setTotal(res.total);
        });
    }, [page]);

    const handleSearch = (term) => {
        setSearch(term);
        setPage(1);
    };

    const configFilters = [
        {
            type: 'search',
            name: 'searchTerm',
            placeholder: 'Rechercher une livraison...'
        }
    ];

    return (
        <MainLayout>
            <h1>Gestion des livraisons</h1>

            <GenericFilters filtersConfig={configFilters} onSearch={handleSearch}/>

            <GenericTable
                columns={columns}
                data={deliveries}
                RowComponent={GenericRow}
                page={page}
                limit={limit}
                total={total}
                onPageChange={setPage}
            />
            <QrModal
                qrCode={selectedQr}
                open={qrModalOpen}
                onClose={() => setQrModalOpen(false)}
            />
        </MainLayout>
    );
}