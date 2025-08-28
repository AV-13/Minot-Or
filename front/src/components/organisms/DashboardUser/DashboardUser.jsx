import React, { useEffect, useState } from 'react';
import InputWithLabel from "../../molecules/InputWithLabel/InputWithLabel";
import Select from "../../atoms/Select/Select";
import { ROLES } from "../../../constants/roles";
import GenericTable from "../GenericTable/GenericTable";
import GenericRow from "../../molecules/GenericRow/GenericRow";
import apiClient from "../../../utils/apiClient";
import Pagination from "../../molecules/Pagination/Pagination";
import Toast from "../../atoms/Toast/Toast";
import styles from './DashboardUser.module.scss';
import GenericFilters from "../GenericFilters/GenericFilters";

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

    const columns = [
        { key: 'email', label: 'Email' },
        { key: 'firstName', label: 'Prénom' },
        { key: 'lastName', label: 'Nom' },
        {
            key: 'role',
            label: 'Rôle',
            render: (value, item) => (
                <Select
                    options={ROLES}
                    value={value}
                    onChange={e => handleRoleChange(item.id, e.target.value)}
                />
            )
        },
        {
            key: 'actions',
            label: 'Actions',
            render: (_, item) => (
                <button
                    className={styles.deleteButton}
                    onClick={() => handleDelete(item.id)}
                >
                    Supprimer
                </button>
            )
        }
    ];

    const filtersConfig = [
        {
            type: 'search',
            name: 'search',
            placeholder: 'Rechercher par email, prénom, nom',
            value: searchInput,
            onChange: setSearchInput
        },
        {
            type: 'select',
            name: 'roleFilter',
            label: 'Filtrer par rôle',
            options: [{ value: '', label: 'Tous' }, ...ROLES],
            value: roleFilterInput,
            onChange: setRoleFilterInput
        }
    ]

    return (
        <div className={styles.container}>
            <GenericFilters filtersConfig={filtersConfig} />

            {loading ? (
                <div className={styles.loadingContainer}>
                    <div className={styles.spinner}></div>
                    <p>Chargement des utilisateurs...</p>
                </div>
            ) : (
                <GenericTable
                    columns={columns}
                    data={users}
                    RowComponent={GenericRow}
                    page={page}
                    limit={limit}
                    total={total}
                    onPageChange={setPage}
                />
            )}

            {toast && <Toast message={toast.message} type={toast.type} />}
        </div>
    );
}