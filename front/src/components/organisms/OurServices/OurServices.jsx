import React from 'react';
import HomeCard from "../../molecules/HomeCard/HomeCard";
import styles from './OurServices.module.scss';

const OurServices = () => (
    <section className={styles['our-services-section']}>
        <h2>Nos services spécialisés</h2>
        <p>
            Minot’Or facilite la gestion de vos commandes de produits de minoterie, la planification des livraisons et l’optimisation de votre chaîne d’approvisionnement. Notre expertise et notre réseau vous garantissent un service fiable, rapide et adapté à vos besoins.
        </p>
        <div className={styles['cards-container']}>
            <HomeCard
                logo="/icons/invoice.svg"
                title="Demandes de devis"
                description="Obtenez rapidement des devis personnalisés pour tous vos besoins en produits de minoterie, accompagné de nos équipes."                buttonLabel="Demander un devis"
                onButtonClick={() => { /* TODO action */ }}
            />
            <HomeCard
                logo="/icons/truck.svg"
                title="Planification de livraisons"
                description="Planifiez vos livraisons selon vos besoins et suivez leur progression en temps réel jusqu'à votre établissement."
                buttonLabel="Planifier une livraison"
                onButtonClick={() => { /* TODO action */ }}
            />
            <HomeCard
                logo="/icons/recycle.svg"
                title="Gestion des invendus"
                description="Signalez facilement vos invendus à collecter et contribuez à notre démarche de réductiopn du gaspillage."
                buttonLabel="Signaler des invendus"
                onButtonClick={() => { /* TODO action */ }}
            />
        </div>
    </section>
);

export default OurServices;
