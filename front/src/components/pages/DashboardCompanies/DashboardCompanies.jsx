// front/src/components/pages/DashboardCompanies/DashboardCompanies.jsx
import React from 'react';
import MainLayout from "../../templates/MainLayout";
import DashboardCompany from "../../organisms/DashboardCompany/DashboardCompany";
import styles from './DashboardCompanies.module.scss';
import PageHeader from "../../molecules/PageHeader/PageHeader";

export default function DashboardCompanies() {
    return (
        <MainLayout>
            <div className={styles.container}>
                <PageHeader
                    title="Gestion des Entreprises"
                    description="Consultez et gérez les entreprises et leurs invendus"
                />
                <DashboardCompany />
            </div>
        </MainLayout>
    );
}