import React, { useEffect, useState } from 'react';
import DashboardUser from "../../organisms/DashboardUser/DashboardUser";
import DashboardProduct from "../../organisms/DashboardProduct/DashboardProduct";
import DashboardWarehouse from "../../organisms/DashboardWarehouse/DashboardWarehouse";
import MainLayout from "../../templates/MainLayout";
import apiClient from "../../../utils/apiClient";

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
        <MainLayout>
            <DashboardUser />
            <DashboardProduct />
            <DashboardWarehouse />
        </MainLayout>
    );
}