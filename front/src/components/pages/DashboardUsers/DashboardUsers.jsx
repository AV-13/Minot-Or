// front/src/components/pages/DashboardUsers/DashboardUsers.jsx
import React from 'react';
import MainLayout from "../../templates/MainLayout";
import DashboardUser from "../../organisms/DashboardUser/DashboardUser";
import styles from './DashboardUsers.module.scss';

export default function DashboardUsers() {
    return (
        <MainLayout>
            <div className={styles.container}>
                <div className={styles.header}>
                    <h1>Gestion des Utilisateurs</h1>
                    <p>Gérez les comptes utilisateurs, leurs rôles et leurs permissions</p>
                </div>
                <DashboardUser />
            </div>
        </MainLayout>
    );
}