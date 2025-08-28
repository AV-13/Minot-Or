import React, { useEffect, useState } from 'react';
import InputWithLabel from '../../molecules/InputWithLabel/InputWithLabel';
import Select from '../../atoms/Select/Select';
import Button from "../../atoms/Button/Button";
import GenericTable from '../../organisms/GenericTable/GenericTable';
import GenericRow from '../../molecules/GenericRow/GenericRow';
import style from './DashboardProduct.module.scss';
import apiClient from '../../../utils/apiClient';
import { TYPES } from '../../../constants/productType';
import AddProductForm from '../../molecules/AddProductForm/AddProductForm';
import Pagination from "../../molecules/Pagination/Pagination";
import GenericFilters from "../GenericFilters/GenericFilters";

export default function DashboardProduct() {
    const [products, setProducts] = useState([]);
    const [search, setSearch] = useState('');
    const [searchInput, setSearchInput] = useState('');
    const [typeFilter, setTypeFilter] = useState('');
    const [typeFilterInput, setTypeFilterInput] = useState('');
    const [loading, setLoading] = useState(true);
    const [showForm, setShowForm] = useState(false);
    const [page, setPage] = useState(1);
    const [limit] = useState(20);
    const [total, setTotal] = useState(0);
    const [editingProduct, setEditingProduct] = useState(null);

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

    const handleEdit = async (product) => {
        // Mettre à jour le produit existant
        await apiClient.put(`/products/${product.id}`, product);
        fetchProducts();
        setEditingProduct(null);
        setShowForm(false);
    };

    const handleDelete = async (id) => {
        await apiClient.delete(`/products/${id}`);
        fetchProducts();
    };

    const handleSearch = () => {
        setPage(1);
        setSearch(searchInput);
        setTypeFilter(typeFilterInput);
    };

    const handleEditClick = (product) => {
        setEditingProduct(product);
        setShowForm(true);
    };

    return (
        <div>
            <Button customClass={style.createButton} onClick={() => {
                if (showForm && !editingProduct) {
                    // Si le formulaire est ouvert pour un ajout, on le ferme
                    setShowForm(false);
                } else if (showForm && editingProduct) {
                    // Si le formulaire est ouvert pour une édition, on annule l'édition
                    setEditingProduct(null);
                    setShowForm(false);
                } else {
                    // Sinon on ouvre le formulaire pour un ajout
                    setShowForm(true);
                }
            }}>
                {showForm ? 'Annuler' : 'Ajouter un produit'}
            </Button>
            {showForm && (
                <AddProductForm
                    onSubmit={editingProduct ? handleEdit : handleAdd}
                    initialValues={editingProduct}
                    isEditing={!!editingProduct}
                />
            )}
            <GenericFilters filtersConfig={filtersConfig}/>
            {loading ? <p>Chargement...</p> :
                <GenericTable
                    columns={columns}
                    data={products}
                    RowComponent={GenericRow}
                    page={page}
                    limit={limit}
                    total={total}
                    onPageChange={setPage}
                />
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