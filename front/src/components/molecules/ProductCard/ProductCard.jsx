import React from 'react';
import Card from '../../atoms/Card/Card';
export default function ProductCard({ product, onAddToCart }) {
    return (
        <Card>
            <h3>{product.name}</h3>
            <p>Type : {product.type}</p>
            <p>{product.description}</p>
            {onAddToCart && (
                <button onClick={() => onAddToCart(product)}>Ajouter au devis</button>
            )}
        </Card>
    );
}