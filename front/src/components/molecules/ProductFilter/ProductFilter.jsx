import React from 'react';
import InputWithLabel from '../../molecules/InputWithLabel/InputWithLabel';
export default function ProductFilter({ name, type, onNameChange, onTypeChange, types }) {
    return (
        <div style={{ display: 'flex', gap: 8, marginBottom: 16 }}>
            <InputWithLabel placeholder="Nom du produit" value={name} onChange={e => onNameChange(e.target.value)} />
            <select value={type} onChange={e => onTypeChange(e.target.value)}>
                <option value="">Tous les types</option>
                {types.map(t => (
                    <option key={t} value={t}>{t}</option>
                ))}
            </select>
        </div>
    );
}