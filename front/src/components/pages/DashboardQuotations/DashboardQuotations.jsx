// front/src/components/pages/DashboardQuotations/DashboardQuotations.jsx
import React from 'react';
import MainLayout from "../../templates/MainLayout";
import QuotationManagement from "../QuotationManagement/QuotationManagement";
import styles from './DashboardQuotations.module.scss';

export default function DashboardQuotations() {
    return (
        <MainLayout>
            <div className={styles.container}>
                <QuotationManagement />
            </div>
        </MainLayout>
    );
}