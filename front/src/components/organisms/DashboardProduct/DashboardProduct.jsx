import React, { useEffect, useState } from 'react';
import InputWithLabel from '../../molecules/InputWithLabel/InputWithLabel';
import Select from '../../atoms/Select';
import ProductTable from '../ProductTable/ProductTable';
import apiClient from '../../../utils/apiClient';
import { TYPES } from '../../../constants/productType';
import AddProductForm from '../../molecules/AddProductForm/AddProductForm';

export default function DashboardProduct() {
    const [products, setProducts] = useState([]);
    const [search, setSearch] = useState('');
    const [typeFilter, setTypeFilter] = useState('');
    const [loading, setLoading] = useState(true);
    const [showForm, setShowForm] = useState(false);

    useEffect(() => { fetchProducts(); }, []);

    const fetchProducts = async () => {
        setLoading(true);
        try {
            const res = await apiClient.get('/products');
            setProducts(Array.isArray(res.items) ? res.items : []);
        } catch (e) {
            setProducts([]);
        }
        setLoading(false);
    };

    const handleAdd = async (product) => {
        await apiClient.post('/products', product);
        fetchProducts();
        setShowForm(false);
    };

    const handleDelete = async (id) => {
        await apiClient.delete(`/products/${id}`);
        setProducts(products.filter(p => p.id !== id));
    };

    const filteredProducts = products.filter(p => {
        const matchesSearch =
            (p.productName || '').toLowerCase().includes(search.toLowerCase());
        const matchesType =
            !typeFilter || p.category === typeFilter;
        return matchesSearch && matchesType;
    });

    return (
        <div>
            <h2>Dashboard Produits</h2>
            <button onClick={() => setShowForm(v => !v)}>
                {showForm ? 'Annuler' : 'Ajouter un produit'}
            </button>
            {showForm && <AddProductForm onAdd={handleAdd} />}
            <InputWithLabel
                type="text"
                placeholder="Recherche par nom"
                value={search}
                onChange={e => setSearch(e.target.value)}
            />
            <Select
                options={['', ...TYPES]}
                value={typeFilter}
                onChange={e => setTypeFilter(e.target.value)}
            />
            {loading ? <p>Chargement...</p> :
                <ProductTable products={filteredProducts} onDelete={handleDelete} />
            }
        </div>
    );
}