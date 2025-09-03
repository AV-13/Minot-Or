// front/src/components/pages/DashboardUsers/DashboardUsers.jsx
import React from 'react';
import MainLayout from "../../templates/MainLayout";
import DashboardUser from "../../organisms/DashboardUser/DashboardUser";
import styles from './DashboardUsers.module.scss';
import PageHeader from "../../molecules/PageHeader/PageHeader";

export default function DashboardUsers() {
    return (
        <MainLayout>
            <div className={styles.container}>
                <PageHeader
                    title="Gestion des Utilisateurs"
                    description="Gérez les comptes utilisateurs, leurs rôles et leurs permissions"
                />
                <DashboardUser />
            </div>
        </MainLayout>
    );
}