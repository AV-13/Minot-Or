// front/src/components/pages/DashboardWarehouses/DashboardWarehouses.jsx
import React from 'react';
import MainLayout from "../../templates/MainLayout";
import DashboardWarehouse from "../../organisms/DashboardWarehouse/DashboardWarehouse";
import styles from './DashboardWarehouses.module.scss';

export default function DashboardWarehouses() {
    return (
        <MainLayout>
            <div className={styles.container}>
                <div className={styles.header}>
                    <h1>Gestion des Entrepôts</h1>
                    <p>Gérez les entrepôts et les emplacements de stockage</p>
                </div>
                <DashboardWarehouse />
            </div>
        </MainLayout>
    );
}