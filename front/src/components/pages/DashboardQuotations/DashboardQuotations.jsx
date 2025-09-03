// front/src/components/pages/DashboardQuotations/DashboardQuotations.jsx
import React from 'react';
import MainLayout from "../../templates/MainLayout";
import QuotationManagement from "../QuotationManagement/QuotationManagement";
import styles from './DashboardQuotations.module.scss';
import PageHeader from "../../molecules/PageHeader/PageHeader";

export default function DashboardQuotations() {
    return (
        <MainLayout>
            <PageHeader
                title="Gestion des devis"
                description="Gérez les devis clients en cours et archivés"
            />
            <div className={styles.container}>
                <QuotationManagement />
            </div>
        </MainLayout>
    );
}