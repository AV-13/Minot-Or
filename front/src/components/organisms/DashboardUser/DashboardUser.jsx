import React, { useEffect, useState } from 'react';
import InputWithLabel from "../../molecules/InputWithLabel/InputWithLabel";
import Select from "../../atoms/Select";
import { ROLES } from "../../../constants/roles";
import UserTable from "../UserTable";
import apiClient from "../../../utils/apiClient";
import Pagination from "../../molecules/Pagination/Pagination";

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
        }
        setLoading(false);
    };

    const handleDelete = async (id) => {
        await apiClient.delete(`users/${id}`);
        fetchUsers();
    };

    const handleRoleChange = async (id, newRole) => {
        await apiClient.put(`users/${id}`, { role: newRole });
        fetchUsers();
    };

    const handleSearch = () => {
        setPage(1);
        setSearch(searchInput);
        setRoleFilter(roleFilterInput);
    };

    return (
        <div>
            <div style={{ display: 'flex', gap: 8, alignItems: 'center' }}>
                <InputWithLabel
                    type="text"
                    placeholder="Recherche par email ou nom"
                    value={searchInput}
                    onChange={e => setSearchInput(e.target.value)}
                />
                <Select
                    options={['', ...ROLES]}
                    value={roleFilterInput}
                    onChange={e => setRoleFilterInput(e.target.value)}
                />
                <button onClick={handleSearch}>Rechercher</button>
            </div>
            {loading ? <p>Chargement...</p> :
                <UserTable users={users} onDelete={handleDelete} onRoleChange={handleRoleChange} />
            }
            <Pagination
                page={page}
                limit={limit}
                total={total}
                onPageChange={setPage}
            />
        </div>
    );
}