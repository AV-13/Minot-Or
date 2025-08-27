import React, { useEffect, useState } from 'react';
import InputWithLabel from "../../molecules/InputWithLabel/InputWithLabel";
import Select from "../../atoms/Select";
import { ROLES } from "../../../constants/roles";
import UserTable from "../UserTable/UserTable";
import apiClient from "../../../utils/apiClient";
import Pagination from "../../molecules/Pagination/Pagination";
import Toast from "../../atoms/Toast/Toast";
import styles from './DashboardUser.module.scss';

export default function DashboardUser() {
    const [users, setUsers] = useState([]);
    const [search, setSearch] = useState('');
    const [searchInput, setSearchInput] = useState('');
    const [roleFilter, setRoleFilter] = useState('');
    const [roleFilterInput, setRoleFilterInput] = useState('');
    const [loading, setLoading] = useState(true);
    const [page, setPage] = useState(1);
    const [limit] = useState(20);
    const [total, setTotal] = useState(0);
    const [toast, setToast] = useState(null);

    useEffect(() => { fetchUsers(); }, [page, search, roleFilter]);

    const fetchUsers = async () => {
        setLoading(true);
        try {
            const res = await apiClient.get('/users', {
                params: {
                    page,
                    limit,
                    search,
                    role: roleFilter
                }
            });
            setUsers(Array.isArray(res.items) ? res.items : []);
            setTotal(res.total || 0);
        } catch (e) {
            setUsers([]);
            setTotal(0);
            showToast("Erreur lors du chargement des utilisateurs", "error");
        }
        setLoading(false);
    };

    const handleDelete = async (id) => {
        try {
            await apiClient.delete(`users/${id}`);
            fetchUsers();
            showToast("Utilisateur supprimé avec succès", "success");
        } catch (e) {
            showToast("Erreur lors de la suppression", "error");
        }
    };

    const handleRoleChange = async (id, newRole) => {
        try {
            await apiClient.put(`users/${id}`, { role: newRole });
            fetchUsers();
            showToast("Rôle modifié avec succès", "success");
        } catch (e) {
            showToast("Erreur lors de la modification du rôle", "error");
        }
    };

    const handleSearch = () => {
        setPage(1);
        setSearch(searchInput);
        setRoleFilter(roleFilterInput);
    };

    const showToast = (message, type) => {
        setToast({ message, type });
        setTimeout(() => setToast(null), 3000);
    };

    return (
        <div className={styles.container}>
            <div className={styles.filtersContainer}>
                <div className={styles.filters}>
                    <InputWithLabel
                        type="text"
                        placeholder="Recherche par email ou nom"
                        value={searchInput}
                        onChange={e => setSearchInput(e.target.value)}
                        label="Rechercher"
                        className={styles.searchInput}
                    />
                    <Select
                        options={['', ...ROLES]}
                        value={roleFilterInput}
                        onChange={e => setRoleFilterInput(e.target.value)}
                        label="Filtrer par rôle"
                        className={styles.roleFilter}
                    />
                    <button
                        className={styles.searchButton}
                        onClick={handleSearch}
                    >
                        Rechercher
                    </button>
                </div>
                <div className={styles.results}>
                    {!loading && <span>{total} utilisateur(s) trouvé(s)</span>}
                </div>
            </div>

            {loading ? (
                <div className={styles.loadingContainer}>
                    <div className={styles.spinner}></div>
                    <p>Chargement des utilisateurs...</p>
                </div>
            ) : (
                <UserTable
                    users={users}
                    onDelete={handleDelete}
                    onRoleChange={handleRoleChange}
                />
            )}

            <div className={styles.paginationContainer}>
                <Pagination
                    page={page}
                    limit={limit}
                    total={total}
                    onPageChange={setPage}
                />
            </div>

            {toast && <Toast message={toast.message} type={toast.type} />}
        </div>
    );
}