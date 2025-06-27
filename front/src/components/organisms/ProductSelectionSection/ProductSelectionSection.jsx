import React from 'react';
import SearchProductBar from '../../molecules/SearchProductBar/SearchProductBar';
import CartProductItem from '../../molecules/CartProductItem/CartProductItem';
import Button from '../../atoms/Button/Button';
import styles from './ProductSelectionSection.module.scss';

const ProductSelectionSection = ({ cart, onRemoveFromCart, onQuantityChange, onSearch, onAddProduct }) => {
    return (
        <div className={styles.selectionSection}>
            <div className={styles.sectionHeader}>
                <span className={styles.icon}>🛒</span>
                <h3>Sélection des produits</h3>
            </div>

            <SearchProductBar onSearch={onSearch} />

            <div className={styles.selectedProducts}>
                {cart.map(product => (
                    <CartProductItem
                        key={product.id}
                        product={product}
                        onRemove={onRemoveFromCart}
                        onQuantityChange={onQuantityChange}
                    />
                ))}
            </div>

            <div className={styles.addProductButton}>
                <Button onClick={onAddProduct} customClass={styles.addButton}>
                    <span>+ Ajouter un produit</span>
                </Button>
            </div>
        </div>
    );
};

export default ProductSelectionSection;