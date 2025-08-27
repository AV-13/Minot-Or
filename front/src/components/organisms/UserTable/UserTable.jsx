import React from 'react';
import UserRow from "../../molecules/UserRow/UserRow";
import styles from './UserTable.module.scss';
import EmptyState from "../../atoms/EmptyState/EmptyState";

export default function UserTable({ users, onDelete, onRoleChange }) {
    if (!users.length) {
        return <EmptyState message="Aucun utilisateur trouvé" icon="/icons/users.svg" />;
    }

    return (
        <div className={styles.tableContainer}>
            <table className={styles.table}>
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
        </div>
    );
}