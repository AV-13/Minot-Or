// front/src/components/pages/DashboardProducts/DashboardProducts.jsx
import React from 'react';
import MainLayout from "../../templates/MainLayout";
import DashboardProduct from "../../organisms/DashboardProduct/DashboardProduct";
import styles from './DashboardProducts.module.scss';
import PageHeader from "../../molecules/PageHeader/PageHeader";

export default function DashboardProducts() {
    return (
        <MainLayout>
            <div className={styles.container}>
                <PageHeader
                    title="Gestion des Produits"
                    description="Gérez le catalogue de produits, les prix et les stocks"
                />
                <DashboardProduct />
            </div>
        </MainLayout>
    );
}