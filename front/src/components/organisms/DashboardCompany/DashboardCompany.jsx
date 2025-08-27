// front/src/components/organisms/DashboardCompany/DashboardCompany.jsx
import React, { useEffect, useState } from 'react';
import InputWithLabel from "../../molecules/InputWithLabel/InputWithLabel";
import CompanyTable from "../CompanyTable/CompanyTable";
import apiClient from "../../../utils/apiClient";
import Pagination from "../../molecules/Pagination/Pagination";
import styles from './DashboardCompany.module.scss';

export default function DashboardCompany() {
    const [companies, setCompanies] = useState([]);
    const [search, setSearch] = useState('');
    const [searchInput, setSearchInput] = useState('');
    const [loading, setLoading] = useState(true);
    const [page, setPage] = useState(1);
    const [limit] = useState(10);
    const [total, setTotal] = useState(0);

    useEffect(() => {
        fetchCompanies();
    }, [page, search]);

    const fetchCompanies = async () => {
        setLoading(true);
        try {
            const res = await apiClient.get('/companies', {
                params: {
                    page,
                    limit,
                    search
                }
            });
            setCompanies(Array.isArray(res.items) ? res.items : []);
            setTotal(res.total || 0);
        } catch (e) {
            console.error("Erreur lors du chargement des entreprises:", e);
            setCompanies([]);
            setTotal(0);
        }
        setLoading(false);
    };

    const handleSearch = () => {
        setPage(1);
        setSearch(searchInput);
    };

    const handleMarkRecovered = async (companyId) => {
        try {
            await apiClient.patch(`/companies/${companyId}/unsold`, {
                unsold: false
            });
            // Mettre à jour la liste après la modification
            fetchCompanies();
        } catch (error) {
            console.error("Erreur lors de la mise à jour des invendus:", error);
        }
    };

    return (
        <div className={styles.container}>
            <div className={styles.searchBar}>
                <InputWithLabel
                    type="text"
                    placeholder="Rechercher une entreprise"
                    value={searchInput}
                    onChange={e => setSearchInput(e.target.value)}
                />
                <button className={styles.searchButton} onClick={handleSearch}>Rechercher</button>
            </div>

            {loading ? (
                <p>Chargement des entreprises...</p>
            ) : (
                <CompanyTable
                    companies={companies}
                    onMarkRecovered={handleMarkRecovered}
                />
            )}

            <Pagination
                page={page}
                limit={limit}
                total={total}
                onPageChange={setPage}
            />
        </div>
    );
}