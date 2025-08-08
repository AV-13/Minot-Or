// front/src/components/molecules/CartNotification/CartNotification.jsx
import React from 'react';
import { Link } from 'react-router';
import styles from './CartNotification.module.scss';

const CartNotification = ({ product, onClose }) => {
    return (
        <div className={styles.notification}>
            <div className={styles.content}>
                <div className={styles.message}>
                    <span className={styles.productName}>{product.name}</span> a été ajouté au panier
                </div>
                <div className={styles.actions}>
                    <Link to="/quotation" className={styles.viewCart}>
                        Voir le panier
                    </Link>
                    <button onClick={onClose} className={styles.closeBtn}>
                        ×
                    </button>
                </div>
            </div>
        </div>
    );
};

export default CartNotification;