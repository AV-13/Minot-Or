import React from 'react';
import Card from '../../atoms/Card/Card';
export default function ProductCard({ product }) {
    return (
        <Card>
            <h3>{product.name}</h3>
            <p>Type : {product.type}</p>
            <p>{product.description}</p>
        </Card>
    );
}