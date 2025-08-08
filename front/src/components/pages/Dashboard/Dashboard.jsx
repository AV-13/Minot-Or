import React from 'react';
import MainLayout from "../../templates/MainLayout";
import DashboardCard from '../../molecules/DashboardCard/DashboardCard';
import styles from './Dashboard.module.scss';

export default function Dashboard() {
    const dashboards = [
        {
            title: 'Utilisateurs',
            description: 'Gérez les utilisateurs, leurs rôles et leurs permissions.',
            icon: '/icons/users.svg',
            link: '/dashboard/users'
        },
        {
            title: 'Produits',
            description: 'Gérez le catalogue de produits, les prix et les stocks.',
            icon: '/icons/products.svg',
            link: '/dashboard/products'
        },
        {
            title: 'Entrepôts',
            description: 'Gérez les entrepôts et les emplacements de stockage.',
            icon: '/icons/warehouse.svg',
            link: '/dashboard/warehouses'
        },
        {
            title: 'Devis',
            description: 'Consultez et gérez les devis clients en cours et archivés.',
            icon: '/icons/quotation.svg',
            link: '/dashboard/quotations'
        },
        {
            title: 'Entreprises',
            description: 'Gérez les entreprises et surveillez les invendus signalés.',
            icon: '/icons/building.svg', // Vous devrez ajouter cette icône
            link: '/dashboard/companies'
        }
    ];

    return (
        <MainLayout>
            <div className={styles.dashboardContainer}>
                <div className={styles.header}>
                    <h1>Tableau de bord</h1>
                    <p>Sélectionnez une section pour accéder à sa gestion détaillée</p>
                </div>

                <div className={styles.cardsGrid}>
                    {dashboards.map((dashboard, index) => (
                        <DashboardCard
                            key={index}
                            title={dashboard.title}
                            description={dashboard.description}
                            icon={dashboard.icon}
                            link={dashboard.link}
                        />
                    ))}
                </div>
            </div>
        </MainLayout>
    );
}