// front/src/components/organisms/OrderedProducts/OrderedProducts.jsx
import React from 'react';
import styles from './OrderedProducts.module.scss';

const OrderedProducts = ({ products }) => {
    if (!products || products.length === 0) {
        return <div className={styles.emptyProducts}>Aucun produit dans cette commande</div>;
    }

    // Calculs financiers
    const subTotal = products.reduce((sum, item) => sum + (item.grossPrice * item.productQuantity), 0);
    const vatRate = 0.055; // 5.5%
    const vat = subTotal * vatRate;
    const shippingCost = 0; // Gratuit
    const total = subTotal + vat + shippingCost;

    return (
        <div className={styles.productsCard}>
            <div className={styles.sectionHeader}>
                <span className={styles.icon}>📦</span>
                <h3>Produits commandés</h3>
            </div>

            <div className={styles.productsList}>
                {products.map(product => (
                    <div key={product.id} className={styles.productItem}>
                        <div className={styles.productInfo}>
                            <span className={styles.productName}>{product.name}</span>
                            <span className={styles.productQuantity}>{product.productQuantity} × {product.unit || 'Sac 25kg'}</span>
                        </div>
                        <div className={styles.productPrice}>
                            {(product.grossPrice * product.productQuantity).toFixed(2)} €
                        </div>
                    </div>
                ))}
            </div>

            <div className={styles.summaryDetails}>
                <div className={styles.summaryRow}>
                    <span>Sous-total</span>
                    <span>{subTotal.toFixed(2)} €</span>
                </div>
                <div className={styles.summaryRow}>
                    <span>TVA (5.5%)</span>
                    <span>{vat.toFixed(2)} €</span>
                </div>
                <div className={styles.summaryRow}>
                    <span>Frais de livraison</span>
                    <span>{shippingCost === 0 ? 'Gratuit' : `${shippingCost} €`}</span>
                </div>
                <div className={styles.totalRow}>
                    <span>Total</span>
                    <span className={styles.totalPrice}>{total.toFixed(2)} €</span>
                </div>
            </div>
        </div>
    );
};

export default OrderedProducts;