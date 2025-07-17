// front/src/components/pages/DashboardQuotations/DashboardQuotations.jsx
import React from 'react';
import MainLayout from "../../templates/MainLayout";
import QuotationManagement from "../QuotationManagement/QuotationManagement";
import styles from './DashboardQuotations.module.scss';

export default function DashboardQuotations() {
    return (
        <MainLayout>
            <div className={styles.container}>
                <div className={styles.header}>
                    <h1>Gestion des Devis</h1>
                    <p>Consultez et gérez les devis clients en cours et archivés</p>
                </div>
                <QuotationManagement />
            </div>
        </MainLayout>
    );
}