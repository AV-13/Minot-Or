import React, { useState, useEffect } from 'react';
import InputWithLabel from '../../molecules/InputWithLabel/InputWithLabel';
import Select from '../../atoms/Select';
import { TYPES } from '../../../constants/productType';
import styles from './AddProductForm.module.scss';

const AddProductForm = ({ onSubmit, initialValues, isEditing }) => {
    const [formData, setFormData] = useState({
        name: '',
        type: '',
        price: '',
        description: '',
        // Autres champs nécessaires
    });

    useEffect(() => {
        if (initialValues) {
            setFormData({
                name: initialValues.name || '',
                type: initialValues.type || '',
                price: initialValues.price || '',
                description: initialValues.description || '',
                // Autres champs à préremplir
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
            // Si on édite, on inclut l'ID
            productData.id = initialValues.id;
        }

        onSubmit(productData);
    };

    return (
        <form className={styles.form} onSubmit={handleSubmit}>
            <h3>{isEditing ? 'Modifier le produit' : 'Ajouter un produit'}</h3>

            <InputWithLabel
                label="Nom"
                name="name"
                value={formData.name}
                onChange={handleChange}
                required
            />

            <Select
                label="Type"
                name="type"
                options={TYPES}
                value={formData.type}
                onChange={handleChange}
                required
            />

            <InputWithLabel
                label="Prix"
                type="number"
                name="price"
                value={formData.price}
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

            {/* Autres champs */}

            <div className={styles.formActions}>
                <button type="submit">
                    {isEditing ? 'Mettre à jour' : 'Ajouter'}
                </button>
            </div>
        </form>
    );
};

export default AddProductForm;