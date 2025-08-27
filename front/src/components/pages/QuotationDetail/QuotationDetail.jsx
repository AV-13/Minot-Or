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
    const [deliveryPrice, setDeliveryPrice] = useState(0);
    const [products, setProducts] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        const fetchQuotationDetails = async () => {
            try {
                setLoading(true);

                const quotationResponse = await apiClient.get(`/quotations/${id}`);
                setDeliveryPrice(quotationResponse.deliveryFee || 0);

                // 1. Récupérer les informations du devis
                const salesListResponse = await apiClient.get(`/salesLists/${quotationResponse.salesListId}`);
                setQuotation(salesListResponse);

                // 2. Récupérer les informations de livraison
                const deliveryResponse = await apiClient.get(`/deliveries/salesLists/${quotationResponse.salesListId}`);
                setDelivery(deliveryResponse);

                // 3. Récupérer les produits associés
                const productsResponse = await apiClient.get(`/salesLists/${quotationResponse.salesListId}/products`);
                setProducts(productsResponse);

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
        navigate('/dashboard/quotations');
    };

    if (loading) {
        return (
            <MainLayout>
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
            <div className={styles.pageHeader}>
                <h1>Détails de votre devis</h1>
                <p>Récapitulatif de votre demande de devis et informations de livraison</p>
            </div>

            <div className={styles.pageContent}>

                <div className={styles.detailsContainer}>
                    <div className={styles.leftColumn}>
                        <QuotationDetailInfo quotation={quotation} />
                        <DeliveryDetailInfo delivery={delivery} />
                    </div>

                    <div className={styles.rightColumn}>
                        <OrderedProducts products={products} quotation={quotation} deliveryPrice={deliveryPrice} />
                    </div>
                </div>
            </div>

        </MainLayout>
    );
}