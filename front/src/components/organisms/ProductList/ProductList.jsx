import React from 'react';
import ProductCard from '../../molecules/ProductCard/ProductCard';
export default function ProductList({ products, onAddToCart, loading }) {
    if (loading) return <p>Chargement des produits...</p>;
    if (!products.length) return <p>Aucun produit trouvé.</p>;
    return (
        <div style={{ display: 'flex', flexWrap: 'wrap' }}>
            {products.map(p => <ProductCard key={p.id} product={p} onAddToCart={onAddToCart} />)}
        </div>
    );
}