import React, { useEffect, useState } from 'react';
import InputWithLabel from '../molecules/InputWithLabel/InputWithLabel';
import Select from '../atoms/Select';
import UserTable from '../organisms/UserTable';
import { ROLES } from '../../constants/roles';
import apiClient from "../../utils/apiClient";

export default function Dashboard() {
    const [users, setUsers] = useState([]);
    const [search, setSearch] = useState('');
    const [roleFilter, setRoleFilter] = useState('');
    const [loading, setLoading] = useState(true);

    useEffect(() => { fetchUsers(); }, []);

    const fetchUsers = async () => {
        setLoading(true);
        try {
            const res = await apiClient.get('/users');
            console.log(res);
            setUsers(Array.isArray(res.items) ? res.items : []);
        } catch (e) {
            setUsers([]);
        }
        setLoading(false);
    };

    const handleDelete = async (id) => {
        await apiClient.delete(`users/${id}`);
        setUsers(users.filter(u => u.id !== id));
    };

    const handleRoleChange = async (id, newRole) => {
        await apiClient.put(`users/${id}`, { role: newRole });
        setUsers(users.map(u => u.id === id ? { ...u, roles: [`ROLE_${newRole.toUpperCase()}`] } : u));
    };

    const filteredUsers = users.filter(u => {
        console.log("role user : ", u.role, "roleFilter :", roleFilter, "search :", search);
        const matchesSearch =
            u.email.toLowerCase().includes(search.toLowerCase()) ||
            (u.name && u.name.toLowerCase().includes(search.toLowerCase()));
        const matchesRole =
            !roleFilter ||
            u.role.replace(/^ROLE_/, '').toLowerCase() === roleFilter.toLowerCase();
        return matchesSearch && matchesRole;
    });

    return (
        <div>
            <h2>Dashboard Utilisateurs</h2>
            <InputWithLabel
                type="text"
                placeholder="Recherche par email ou nom"
                value={search}
                onChange={e => setSearch(e.target.value)}
            />
            <Select
                options={['', ...ROLES]}
                value={roleFilter}
                onChange={e => setRoleFilter(e.target.value)}
            />
            {loading ? <p>Chargement...</p> :
                <UserTable users={filteredUsers} onDelete={handleDelete} onRoleChange={handleRoleChange} />
            }
        </div>
    );
}