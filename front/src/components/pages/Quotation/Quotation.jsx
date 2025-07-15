import React, { useState, useEffect } from 'react';
import { useCart } from '../../../contexts/CartContext';
import Header from '../../organisms/Header/Header';
import Footer from '../../organisms/Footer/Footer';
import MainLayout from '../../templates/MainLayout';
import ProductSelectionSection from '../../organisms/ProductSelectionSection/ProductSelectionSection';
import DeliveryInfoSection from '../../organisms/DeliveryInfoSection/DeliveryInfoSection';
import QuotationSummary from '../../organisms/QuotationSummary/QuotationSummary';
import styles from './Quotation.module.scss';
import { useNavigate } from "react-router";
import apiClient from "../../../utils/apiClient";

export default function Quotation() {
    const { cart, removeFromCart, updateQuantity } = useCart();
    const navigate = useNavigate();
    const [cartItems, setCartItems] = useState([]);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [deliveryInfo, setDeliveryInfo] = useState({
        deliveryDate: '',
        timeSlot: 'morning',
        address: '',
        instructions: ''
    });

    // Calculs financiers
    const subTotal = cartItems.reduce((sum, item) => sum + item.totalPrice, 0);
    const vatRate = 0.055; // 5.5%
    const vat = subTotal * vatRate;
    const shippingCost = 0; // Gratuit ???
    const total = subTotal + vat + shippingCost;

    useEffect(() => {
        // Transformer les éléments du panier pour leur donner une structure adaptée à l'affichage
        const formattedCart = cart.map(item => ({
            ...item,
            unit: 'Sac 25kg',
            totalPrice: item.price * item.quantity
        }));
        setCartItems(formattedCart);
    }, [cart]);

    const handleQuantityChange = (productId, newQuantity) => {
        updateQuantity(productId, newQuantity);
        setCartItems(cartItems.map(item =>
            item.id === productId ? { ...item, quantity: newQuantity, totalPrice: item.price * newQuantity } : item
        ));
    };

    const handleDeliveryInfoChange = (field, value) => {
        setDeliveryInfo({
            ...deliveryInfo,
            [field]: value
        });
    };

    const handleSearch = (query) => {
        console.log('Searching for:', query);
        // Logique de recherche à implémenter
    };

    const handleAddProduct = () => {
        navigate('/product');
    };

    const handleSubmitQuotation = async () => {
        if (cart.length === 0) {
            alert('Veuillez ajouter des produits à votre demande de devis.');
            return;
        }

        if (!deliveryInfo.deliveryDate) {
            alert('Veuillez sélectionner une date de livraison.');
            return;
        }

        setIsSubmitting(true);

        try {
            // 1. Créer une liste de vente (SalesList)
            const salesListResponse = await apiClient.post('/salesLists', {
                productsPrice: subTotal,
                globalDiscount: 0,
                status: 'pending',
                expirationDate: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0], // 30 jours
            });

            const salesListId = salesListResponse.id;

            // 2. Ajouter les produits à la liste de vente
            for (const item of cart) {
                await apiClient.post(`/salesLists/${salesListId}/products`, {
                    productId: item.id,
                    productQuantity: item.quantity,
                    productDiscount: 0
                });
            }

            // 3. Créer les informations de livraison
            await apiClient.post(`/salesLists/${salesListId}/delivery`, {
                deliveryDate: deliveryInfo.deliveryDate,
                deliveryAddress: deliveryInfo.address || 'Adresse par défaut',
                deliveryStatus: 'pending',
                driverRemark: deliveryInfo.instructions || ''
            });

            // 4. Créer le devis
            await apiClient.post(`/salesLists/${salesListId}/quotation`, {
                dueDate: deliveryInfo.deliveryDate,
                distance: 10 // Distance par défaut en km pour le calcul
            });

            // Redirection vers une page de confirmation
            navigate('/devis/confirmation', { state: { quotationId: salesListId } });

        } catch (error) {
            console.error('Erreur lors de la soumission du devis:', error);
            alert('Une erreur est survenue lors de la soumission de votre demande de devis.');
        } finally {
            setIsSubmitting(false);
        }
    };

    const handleSaveQuotation = () => {
        console.log('Saving quotation to favorites');
        // Sauvegarder le devis dans les favoris de l'utilisateur
    };

    return (
        <MainLayout>
            <Header />

            <div className={styles.pageHeader}>
                <h1>Demande de devis</h1>
                <p>Préparez votre devis : sélectionnez les produits, les quantités, et votre date de livraison souhaitée.</p>
            </div>

            <div className={styles.pageContent}>
                <div className={styles.leftColumn}>
                    <ProductSelectionSection
                        cart={cartItems}
                        onRemoveFromCart={removeFromCart}
                        onQuantityChange={handleQuantityChange}
                        onSearch={handleSearch}
                        onAddProduct={handleAddProduct}
                    />

                    <DeliveryInfoSection
                        onInfoChange={handleDeliveryInfoChange}
                    />
                </div>

                <div className={styles.rightColumn}>
                    <QuotationSummary
                        cart={cartItems}
                        subtotal={subTotal.toFixed(2)}
                        vat={vat.toFixed(2)}
                        shippingCost={shippingCost}
                        total={total.toFixed(2)}
                        onSubmitQuotation={handleSubmitQuotation}
                        onSaveQuotation={handleSaveQuotation}
                    />
                </div>
            </div>

            <Footer />
        </MainLayout>
    );
}