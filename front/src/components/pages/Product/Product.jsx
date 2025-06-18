import React, { useState, useMemo } from 'react';
import ProductFilter from '../../molecules/ProductFilter/ProductFilter';
import ProductList from '../../organisms/ProductList/ProductList';

// Exemple de données
const PRODUCTS = [
    { id: 1, name: 'Produit A', type: 'Type 1', description: 'Description A' },
    { id: 2, name: 'Produit B', type: 'Type 2', description: 'Description B' },
    { id: 3, name: 'Produit C', type: 'Type 1', description: 'Description C' },
];

const TYPES = [...new Set(PRODUCTS.map(p => p.type))];

export default function Product() {
    const [name, setName] = useState('');
    const [type, setType] = useState('');

    const filtered = useMemo(() =>
        PRODUCTS.filter(p =>
            (!name || p.name.toLowerCase().includes(name.toLowerCase())) &&
            (!type || p.type === type)
        ), [name, type]
    );

    return (
        <div>
            <h2>Produits</h2>
            <ProductFilter
                name={name}
                type={type}
                onNameChange={setName}
                onTypeChange={setType}
                types={TYPES}
            />
            <ProductList products={filtered} />
        </div>
    );
}