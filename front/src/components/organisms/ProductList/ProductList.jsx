import React from 'react';
import ProductCard from '../../molecules/ProductCard/ProductCard';
export default function ProductList({ products }) {
    if (!products.length) return <p>Aucun produit trouvé.</p>;
    return (
        <div style={{ display: 'flex', flexWrap: 'wrap' }}>
            {products.map(p => <ProductCard key={p.id} product={p} />)}
        </div>
    );
}