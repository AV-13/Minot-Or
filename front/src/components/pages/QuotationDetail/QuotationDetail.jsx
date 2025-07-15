// front/src/components/pages/QuotationDetail/QuotationDetail.jsx
import React, { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router';
import Header from '../../organisms/Header/Header';
import Footer from '../../organisms/Footer/Footer';
import MainLayout from '../../templates/MainLayout';
import QuotationDetailInfo from '../../organisms/QuotationDetailInfo/QuotationDetailInfo';
import DeliveryDetailInfo from '../../organisms/DeliveryDetailInfo/DeliveryDetailInfo';
import OrderedProducts from '../../organisms/OrderedProducts/OrderedProducts';
import Button from '../../atoms/Button/Button';
import apiClient from "../../../utils/apiClient";
import styles from './QuotationDetail.module.scss';

export default function QuotationDetail() {
    const { id } = useParams();
    const navigate = useNavigate();
    const [quotation, setQuotation] = useState(null);
    const [delivery, setDelivery] = useState(null);
    const [products, setProducts] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        const fetchQuotationDetails = async () => {
            try {
                setLoading(true);

                // 1. Récupérer les informations du devis
                const quotationResponse = await apiClient.get(`/salesLists/${id}`);
                setQuotation(quotationResponse.data);

                // 2. Récupérer les informations de livraison
                const deliveryResponse = await apiClient.get(`/deliveries/salesLists/${id}`);
                console.log(deliveryResponse);
                setDelivery(deliveryResponse.data);

                // 3. Récupérer les produits associés
                const productsResponse = await apiClient.get(`/salesLists/${id}/products`);
                setProducts(productsResponse.data);

                setLoading(false);
            } catch (err) {
                console.error('Erreur lors de la récupération des détails du devis:', err);
                setError('Impossible de charger les détails du devis. Veuillez réessayer.');
                setLoading(false);
            }
        };

        if (id) {
            fetchQuotationDetails();
        }
    }, [id]);

    const handleBackToList = () => {
        navigate('/quotations');
    };

    if (loading) {
        return (
            <MainLayout>
                <Header />
                <div className={styles.loadingContainer}>
                    <p>Chargement des détails du devis...</p>
                </div>
                <Footer />
            </MainLayout>
        );
    }

    if (error) {
        return (
            <MainLayout>
                <Header />
                <div className={styles.errorContainer}>
                    <p>{error}</p>
                    <Button onClick={handleBackToList}>Retour à la liste des devis</Button>
                </div>
                <Footer />
            </MainLayout>
        );
    }

    return (
        <MainLayout>
            <Header />

            <div className={styles.pageHeader}>
                <h1>Détails de votre devis</h1>
                <p>Récapitulatif de votre demande de devis et informations de livraison</p>
            </div>

            <div className={styles.pageContent}>
                <div className={styles.backLink}>
                    <Button onClick={handleBackToList} customClass={styles.backButton}>
                        ← Retour à la liste des devis
                    </Button>
                </div>

                <div className={styles.detailsContainer}>
                    <div className={styles.leftColumn}>
                        <QuotationDetailInfo quotation={quotation} />
                        <DeliveryDetailInfo delivery={delivery} />
                    </div>

                    <div className={styles.rightColumn}>
                        <OrderedProducts products={products} />
                    </div>
                </div>
            </div>

            <Footer />
        </MainLayout>
    );
}