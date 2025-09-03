import React, { useEffect, useState } from 'react';
import MainLayout from '../../templates/MainLayout';
import GenericTable from '../../organisms/GenericTable/GenericTable';
import GenericRow from '../../molecules/GenericRow/GenericRow';
import QrModal from "../../atoms/QrModal/QrModal";
import Button from "../../atoms/Button/Button";
import apiClient from '../../../utils/apiClient';
import PageHeader from "../../molecules/PageHeader/PageHeader";
import GenericFilters from "../../organisms/GenericFilters/GenericFilters";
import style from './DashboardDeliveries.module.scss';
import Loader from "../../atoms/Loader/Loader";

export default function DashboardDeliveries() {
    const [qrModalOpen, setQrModalOpen] = useState(false);
    const [selectedQr, setSelectedQr] = useState('');
    const [deliveries, setDeliveries] = useState([]);
    const [page, setPage] = useState(1);
    const [search, setSearch] = useState('');
    const [limit] = useState(10);
    const [total, setTotal] = useState(0);
    const [loading, setLoading] = useState(false); // Ajout loader


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
        setLoading(true);
        apiClient.get('/deliveries', { params: { page, limit, search } })
            .then(res => {
                setDeliveries(res.items);
                setTotal(res.total);
            })
            .finally(() => setLoading(false));
    }, [page, search]);

    const handleSearch = (values) => {
        setSearch(values.searchTerm || '');
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
            <PageHeader
                title="Gestion des livraisons"
                description="Suivez l'état des livraisons en temps réel."
            />
            <GenericFilters filtersConfig={configFilters} onSearch={handleSearch}/>

            {loading ? (
                <div className={style.loaderContainer}>
                    <Loader />
                </div>
            ) : (
                <GenericTable
                    columns={columns}
                    data={deliveries}
                    RowComponent={GenericRow}
                    page={page}
                    limit={limit}
                    total={total}
                    onPageChange={setPage}
                />
            )}
            <GenericFilters filtersConfig={configFilters} onSearch={handleSearch}/>

            {loading ? (
                <div className={style.loaderContainer}>
                    <Loader />
                </div>
            ) : (
                <GenericTable
                    columns={columns}
                    data={deliveries}
                    RowComponent={GenericRow}
                    page={page}
                    limit={limit}
                    total={total}
                    onPageChange={setPage}
                />
            )}
            <QrModal
                qrCode={selectedQr}
                open={qrModalOpen}
                onClose={() => setQrModalOpen(false)}
            />
        </MainLayout>
    );
}