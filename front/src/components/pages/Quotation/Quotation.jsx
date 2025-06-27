import React, { useState, useEffect } from 'react';
import { useCart } from '../../../contexts/CartContext';
import Header from '../../organisms/Header/Header';
import Footer from '../../organisms/Footer/Footer';
import MainLayout from '../../templates/MainLayout';
import ProductSelectionSection from '../../organisms/ProductSelectionSection/ProductSelectionSection';
import DeliveryInfoSection from '../../organisms/DeliveryInfoSection/DeliveryInfoSection';
import QuotationSummary from '../../organisms/QuotationSummary/QuotationSummary';
import styles from './Quotation.module.scss';

export default function Quotation() {
    const { cart, removeFromCart, updateQuantity } = useCart();
    const [cartItems, setCartItems] = useState([]);
    const [deliveryInfo, setDeliveryInfo] = useState({
        deliveryDate: '',
        timeSlot: 'morning',
        address: '',
        instructions: ''
    });

    // Calculs financiers
    const subtotal = cartItems.reduce((sum, item) => sum + item.totalPrice, 0);
    const vatRate = 0.055; // 5.5%
    const vat = subtotal * vatRate;
    const shippingCost = 0; // Gratuit pour cet exemple
    const total = subtotal + vat + shippingCost;

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
        console.log('Add product clicked');
        // Ouvrir une modal ou naviguer vers une page de sélection de produits
    };

    const handleSubmitQuotation = () => {
        const quotationData = {
            products: cartItems,
            deliveryInfo,
            totals: {
                subtotal,
                vat,
                shippingCost,
                total
            }
        };

        console.log('Submitting quotation:', quotationData);
        // Envoyer la demande de devis à l'API
    };

    const handleSaveQuotation = () => {
        console.log('Saving quotation to favorites');
        // Sauvegarder le devis dans les favoris de l'utilisateur
    };

    return (
        <MainLayout>
            <Header />

            <div className={styles.breadcrumb}>
                <a href="/accueil">Accueil</a> &gt; <a href="/devis">Devis</a> &gt; <span>Nouvelle demande</span>
            </div>

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
                        subtotal={subtotal.toFixed(2)}
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