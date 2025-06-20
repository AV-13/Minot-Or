import React, { useState } from 'react';
import { TYPES } from '../../../constants/productType';

export default function AddProductForm({ onAdd }) {
    const [productName, setProductName] = useState('');
    const [category, setCategory] = useState('');
    const [netPrice, setNetPrice] = useState('');
    const [grossPrice, setGrossPrice] = useState('');
    const [stockQuantity, setStockQuantity] = useState('');
    const [unitWeight, setUnitWeight] = useState('');

    const handleSubmit = (e) => {
        e.preventDefault();
        if (!productName || !category) return;
        onAdd({
            productName,
            category,
            netPrice: parseFloat(netPrice),
            grossPrice: parseFloat(grossPrice),
            stockQuantity: parseInt(stockQuantity, 10),
            unitWeight: parseFloat(unitWeight),
        });
        setProductName('');
        setCategory('');
        setNetPrice('');
        setGrossPrice('');
        setStockQuantity('');
        setUnitWeight('');
    };

    return (
        <form onSubmit={handleSubmit} style={{ display: 'flex', gap: 8, marginBottom: 16, flexWrap: 'wrap' }}>
            <input placeholder="Nom" value={productName} onChange={e => setProductName(e.target.value)} required />
            <select value={category} onChange={e => setCategory(e.target.value)} required>
                <option value="">Catégorie</option>
                {TYPES.map(t => <option key={t} value={t}>{t}</option>)}
            </select>
            <input type="number" step="0.01" placeholder="Prix net" value={netPrice} onChange={e => setNetPrice(e.target.value)} />
            <input type="number" step="0.01" placeholder="Prix brut" value={grossPrice} onChange={e => setGrossPrice(e.target.value)} />
            <input type="number" placeholder="Stock" value={stockQuantity} onChange={e => setStockQuantity(e.target.value)} />
            <input type="number" step="0.01" placeholder="Poids unitaire" value={unitWeight} onChange={e => setUnitWeight(e.target.value)} />
            <button type="submit">Ajouter</button>
        </form>
    );
}