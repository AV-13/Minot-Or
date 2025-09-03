import React from 'react';
import MainLayout from "../../templates/MainLayout";
import DashboardCard from '../../molecules/DashboardCard/DashboardCard';
import styles from './Dashboard.module.scss';
import PageHeader from "../../molecules/PageHeader/PageHeader";

export default function Dashboard() {
    const dashboards = [
        {
            title: 'Utilisateurs',
            description: 'Gérez les utilisateurs, leurs rôles et leurs permissions.',
            icon: '/icons/user.svg',
            link: '/dashboard/users'
        },
        {
            title: 'Produits',
            description: 'Gérez le catalogue de produits, les prix et les stocks.',
            icon: '/icons/cart.svg',
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
            icon: '/icons/building.svg',
            link: '/dashboard/companies'
        },
        {
            title: 'Camions',
            description: 'Gérez les camions, leurs statuts et leurs entretiens.',
            icon: '/icons/truck.svg', // Vous devrez ajouter cette icône
            link: '/dashboard/trucks'
        },
        {
            title: 'Livraisons',
            description: 'Consultez et gérez les livraisons en cours et passées.',
            icon: '/icons/package.svg', // Ajoutez l’icône correspondante
            link: '/dashboard/deliveries'
        }
    ];

    return (
        <MainLayout>
            <div className={styles.dashboardContainer}>
                <PageHeader
                    title="Tableau de bord"
                    description="Sélectionnez une section pour accéder à sa gestion détaillée"
                />
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