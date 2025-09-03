// front/src/components/pages/DashboardWarehouses/DashboardWarehouses.jsx
import React from 'react';
import MainLayout from "../../templates/MainLayout";
import DashboardWarehouse from "../../organisms/DashboardWarehouse/DashboardWarehouse";
import styles from './DashboardWarehouses.module.scss';
import PageHeader from "../../molecules/PageHeader/PageHeader";

export default function DashboardWarehouses() {
    return (
        <MainLayout>
            <div className={styles.container}>
                <PageHeader
                    title="Gestion des Entrepôts"
                    description="Gérez les entrepôts et les emplacements de stockage"
                />
                <DashboardWarehouse />
            </div>
        </MainLayout>
    );
}