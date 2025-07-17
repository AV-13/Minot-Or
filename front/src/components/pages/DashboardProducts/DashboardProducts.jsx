// front/src/components/pages/DashboardProducts/DashboardProducts.jsx
import React from 'react';
import MainLayout from "../../templates/MainLayout";
import DashboardProduct from "../../organisms/DashboardProduct/DashboardProduct";
import styles from './DashboardProducts.module.scss';

export default function DashboardProducts() {
    return (
        <MainLayout>
            <div className={styles.container}>
                <div className={styles.header}>
                    <h1>Gestion des Produits</h1>
                    <p>Gérez le catalogue de produits, les prix et les stocks</p>
                </div>
                <DashboardProduct />
            </div>
        </MainLayout>
    );
}