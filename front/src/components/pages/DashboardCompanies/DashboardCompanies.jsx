// front/src/components/pages/DashboardCompanies/DashboardCompanies.jsx
import React from 'react';
import MainLayout from "../../templates/MainLayout";
import DashboardCompany from "../../organisms/DashboardCompany/DashboardCompany";
import styles from './DashboardCompanies.module.scss';

export default function DashboardCompanies() {
    return (
        <MainLayout>
            <div className={styles.container}>
                <div className={styles.header}>
                    <h1>Gestion des Entreprises</h1>
                    <p>Consultez et gérez les entreprises et leurs invendus</p>
                </div>
                <DashboardCompany />
            </div>
        </MainLayout>
    );
}