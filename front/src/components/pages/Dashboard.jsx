import React, { useEffect, useState } from 'react';
import axios from 'axios';
import Input from '../atoms/Input';
import Select from '../atoms/Select';
import UserTable from '../organisms/UserTable';
import { ROLES } from '../../constants/roles';

export default function Dashboard() {
    const [users, setUsers] = useState([]);
    const [search, setSearch] = useState('');
    const [roleFilter, setRoleFilter] = useState('');
    const [loading, setLoading] = useState(true);

    useEffect(() => { fetchUsers(); }, []);

    const fetchUsers = async () => {
        setLoading(true);
        const res = await axios.get('/users');
        setUsers(res.data);
        setLoading(false);
    };

    const handleDelete = async (id) => {
        await axios.delete(`/api/users/${id}`);
        setUsers(users.filter(u => u.id !== id));
    };

    const handleRoleChange = async (id, newRole) => {
        await axios.put(`/api/users/${id}/role`, { role: newRole });
        setUsers(users.map(u => u.id === id ? { ...u, roles: [`ROLE_${newRole.toUpperCase()}`] } : u));
    };

    const filteredUsers = users.filter(u => {
        const matchesSearch =
            u.email.toLowerCase().includes(search.toLowerCase()) ||
            (u.name && u.name.toLowerCase().includes(search.toLowerCase()));
        const matchesRole =
            !roleFilter ||
            Object.values(u.roles).some(r => r.replace(/^ROLE_/, '').toLowerCase() === roleFilter.toLowerCase());
        return matchesSearch && matchesRole;
    });

    return (
        <div>
            <h2>Dashboard Utilisateurs</h2>
            <Input
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