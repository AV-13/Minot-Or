import React from 'react';
import UserRow from '../molecules/UserRow';

export default function UserTable({ users, onDelete, onRoleChange }) {
    return (
        <table>
            <thead>
            <tr>
                <th>Email</th>
                <th>Nom</th>
                <th>Rôles</th>
                <th>Changer rôle</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            {users.map(u => (
                <UserRow key={u.id} user={u} onDelete={onDelete} onRoleChange={onRoleChange} />
            ))}
            </tbody>
        </table>
    );
}