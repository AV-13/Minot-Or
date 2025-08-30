import React, { useState, useEffect } from 'react';
import InputWithLabel from '../../molecules/InputWithLabel/InputWithLabel';
import Select from '../../atoms/Select/Select';
import { TYPES } from '../../../constants/productType';
import styles from './AddProductForm.module.scss';
import apiClient from '../../../utils/apiClient';

const AddProductForm = ({ onSubmit, initialValues, isEditing }) => {
    const [formData, setFormData] = useState({
        productName: '',
        category: TYPES[0] || '',
        netPrice: '',
        grossPrice: '',
        unitWeight: '',
        quantity: '',
        description: '',
        warehouseId: '',
    });
    const [warehouses, setWarehouses] = useState([]);

    useEffect(() => {
        // Charger la liste des entrepôts
        apiClient.get('/warehouses').then(res => {
            setWarehouses(res.items || []);
        });
    }, []);

    useEffect(() => {
        if (initialValues) {
            setFormData({
                productName: initialValues.productName || '',
                category: initialValues.category || '',
                netPrice: initialValues.netPrice || '',
                grossPrice: initialValues.grossPrice || '',
                unitWeight: initialValues.unitWeight || '',
                quantity: initialValues.quantity || '',
                description: initialValues.description || '',
                warehouseId: initialValues.warehouseId || '',
            });
        }
    }, [initialValues]);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({ ...prev, [name]: value }));
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        const productData = { ...formData };
        if (isEditing) {
            productData.id = initialValues.id;
        }
        onSubmit(productData);
    };

    return (
        <form className={styles.form} onSubmit={handleSubmit}>
            <h3>{isEditing ? 'Modifier le produit' : 'Ajouter un produit'}</h3>
            <InputWithLabel
                label="Nom"
                name="productName"
                value={formData.productName}
                onChange={handleChange}
                required
            />
            <Select
                label="Catégorie"
                name="category"
                options={TYPES.map(t => ({ value: t, label: t }))}
                value={formData.category}
                onChange={handleChange}
                required
            />
            <Select
                label="Entrepôt"
                name="warehouseId"
                options={[{ value: '', label: 'Sélectionner un entrepôt' }, ...warehouses.map(w => ({
                    value: w.id,
                    label: w.warehouseAddress
                }))]}
                value={formData.warehouseId}
                onChange={handleChange}
                required
            />
            <InputWithLabel
                label="Prix net"
                type="number"
                name="netPrice"
                value={formData.netPrice}
                onChange={handleChange}
                required
            />
            <InputWithLabel
                label="Prix TTC"
                type="number"
                name="grossPrice"
                value={formData.grossPrice}
                onChange={handleChange}
                required
            />
            <InputWithLabel
                label="Poids unitaire (kg)"
                type="number"
                name="unitWeight"
                value={formData.unitWeight}
                onChange={handleChange}
                required
            />
            <InputWithLabel
                label="Stock"
                type="number"
                name="quantity"
                value={formData.quantity}
                onChange={handleChange}
                required
            />
            <InputWithLabel
                label="Description"
                type="textarea"
                name="description"
                value={formData.description}
                onChange={handleChange}
            />
            <div className={styles.formActions}>
                <button type="submit">
                    {isEditing ? 'Mettre à jour' : 'Ajouter'}
                </button>
            </div>
        </form>
    );
};

export default AddProductForm;