import React, { useEffect, useState } from 'react';
import InputWithLabel from '../../molecules/InputWithLabel/InputWithLabel';
import Select from '../../atoms/Select';
import ProductTable from '../ProductTable/ProductTable';
import apiClient from '../../../utils/apiClient';
import { TYPES } from '../../../constants/productType';
import AddProductForm from '../../molecules/AddProductForm/AddProductForm';
import Pagination from "../../molecules/Pagination/Pagination";

export default function DashboardProduct() {
    const [products, setProducts] = useState([]);
    const [search, setSearch] = useState('');
    const [searchInput, setSearchInput] = useState('');
    const [typeFilter, setTypeFilter] = useState('');
    const [loading, setLoading] = useState(true);
    const [showForm, setShowForm] = useState(false);
    const [page, setPage] = useState(1);
    const [limit] = useState(20);
    const [total, setTotal] = useState(0);

    useEffect(() => { fetchProducts(); }, [page, search, typeFilter]);

    const fetchProducts = async () => {
        setLoading(true);
        try {
            const res = await apiClient.get('/products', {
                params: {
                    page,
                    limit,
                    search,
                    category: typeFilter
                }
            });
            setProducts(Array.isArray(res.items) ? res.items : []);
            setTotal(res.total || 0);
        } catch (e) {
            setProducts([]);
            setTotal(0);
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
        fetchProducts();
    };

    const handleSearch = () => {
        setPage(1);
        setSearch(searchInput);
    };

    const handleTypeFilter = (e) => {
        setPage(1);
        setTypeFilter(e.target.value);
    };

    return (
        <div>
            <h2>Dashboard Produits</h2>
            <button onClick={() => setShowForm(v => !v)}>
                {showForm ? 'Annuler' : 'Ajouter un produit'}
            </button>
            {showForm && <AddProductForm onAdd={handleAdd} />}
            <div style={{ display: 'flex', gap: 8, alignItems: 'center' }}>
                <InputWithLabel
                    type="text"
                    placeholder="Recherche par nom"
                    value={searchInput}
                    onChange={e => setSearchInput(e.target.value)}
                />
                <Select
                    options={['', ...TYPES]}
                    value={typeFilter}
                    onChange={handleTypeFilter}
                />
                <button onClick={handleSearch}>Rechercher</button>
            </div>
            {loading ? <p>Chargement...</p> :
                <ProductTable products={products} onDelete={handleDelete} />
            }
            <Pagination
                page={page}
                limit={limit}
                total={total}
                onPageChange={setPage}
            />
        </div>
    );
}