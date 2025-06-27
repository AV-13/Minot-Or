import React from 'react';
import Button from '../../atoms/Button/Button';
import styles from './CartProductItem.module.scss';

const CartProductItem = ({ product, onRemove, onQuantityChange }) => {
    return (
        <div className={styles.cartItem}>
            <div className={styles.productImage}>
                <img src={product.imageUrl || '/images/flour-icon.png'} alt={product.name} />
            </div>
            <div className={styles.productInfo}>
                <h4 className={styles.productName}>{product.name}</h4>
                <p className={styles.productDescription}>{product.description}</p>
            </div>
            <div className={styles.quantityControls}>
                <span>Quantité:</span>
                <div className={styles.controls}>
                    <button
                        onClick={() => onQuantityChange(product.id, Math.max(1, product.quantity - 1))}
                        className={styles.quantityBtn}
                    >-</button>
                    <input
                        type="number"
                        value={product.quantity}
                        onChange={(e) => onQuantityChange(product.id, parseInt(e.target.value) || 1)}
                        className={styles.quantityInput}
                    />
                    <button
                        onClick={() => onQuantityChange(product.id, product.quantity + 1)}
                        className={styles.quantityBtn}
                    >+</button>
                </div>
                <span>Unité:</span>
                <select className={styles.unitSelect}>
                    <option>Sac 25kg</option>
                </select>
            </div>
            <div className={styles.price}>{product.price} € / kg</div>
            <button onClick={() => onRemove(product.id)} className={styles.removeBtn}>
                <span className={styles.trashIcon}>🗑️</span>
            </button>
        </div>
    );
};

export default CartProductItem;