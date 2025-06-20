import React from 'react';
import InputWithLabel from '../../molecules/InputWithLabel/InputWithLabel';
import styles from './ProductFilter.module.scss';
import Button from "../../atoms/Button/Button";
import Input from "../../atoms/Input/Input";

export default function ProductFilter({ name, type, onNameChange, onTypeChange, types }) {
    return (
        <div className={styles.filterContainer}>
            <Input className={styles.searchbar} placeholder="Nom du produit" value={name} onChange={e => onNameChange(e.target.value)} />
            <select className={styles.select} value={type} onChange={e => onTypeChange(e.target.value)}>
                <option value="">Tous les types</option>
                {types.map(t => (
                    <option key={t} value={t}>{t}</option>
                ))}
            </select>
            <Button>Filtrer</Button>
        </div>
    );
}
