import React from 'react';
import Card from '../../atoms/Card/Card';
import styles from './ProductCard.module.scss';
import Button from "../../atoms/Button/Button";

const categoryImages = {
    bread: '/images/products/bread.jpg',
    butter: '/images/products/butter.jpg',
    egg: '/images/products/egg.jpg',
    flour: '/images/products/flour.jpg',
    milk: '/images/products/milk.png',
    oil: '/images/products/oil.jpg',
    salt: '/images/products/salt.jpg',
    seed: '/images/products/seed.jpg',
    sugar: '/images/products/sugar.jpg',
    yeast: '/images/products/yeast.jpg',
    chocolate: '/images/products/chocolate.jpg'
};

export default function ProductCard({ product, onAddToCart }) {
    const imageSrc = categoryImages[product.category] || '/images/products/default.jpg';
    console.log("catégorie de l'image :", product, "imageSrc :", imageSrc);
    return (
        <Card>
            <div className={styles.productImageContainer}>
                <img alt={product.name} src={imageSrc} className={styles.productImage} />
            </div>
            <div className={styles.productDetails}>
            <h3 className={styles.productTitle}>{product.name}</h3>
            <p className={styles.description}>La description de mon produit à ajouter une fois que l'on a le champ en base de données.</p>
            <div className={styles.details}>
            <div className={styles.priceContainer}>
                <span className={styles.priceLabel}>Prix au kg</span>
                <span className={styles.price}>{product.netPrice.toFixed(2)} €</span>
            </div>
            {onAddToCart && (
                <Button onClick={() => onAddToCart(product)}>Ajouter au devis</Button>
            )}
            </div>
            </div>
        </Card>
    );
}
