// front/src/components/organisms/GenericFilters/GenericFilters.jsx
import React, { useState } from 'react';
import styles from './GenericFilters.module.scss';
import Button from "../../atoms/Button/Button";

const GenericFilters = ({ filtersConfig, onSearch, onFilterChange }) => {
    const initialValues = {}; // ou définissez les valeurs par défaut selon filtersConfig
    const [values, setValues] = useState(initialValues);

    const handleChange = (name, value) => {
        const newValues = { ...values, [name]: value };
        setValues(newValues);
        // onFilterChange(newValues);
    };

    const handleReset = () => {
        setValues(initialValues);
    };

    const handleSearch = (e) => {
        e.preventDefault();
        onSearch(values);
    };

    return (
        <div className={styles.filtersContainer}>
            <form onSubmit={handleSearch} className={styles.searchContainer}>
                {filtersConfig.map(filter => {
                    if (filter.type === 'search') {
                        return (
                            <input
                                key={filter.name}
                                type="text"
                                placeholder={filter.placeholder}
                                value={values[filter.name] || ''}
                                onChange={e => handleChange(filter.name, e.target.value)}
                                className={styles.searchInput}
                            />
                        );
                    }
                    if (filter.type === 'select') {
                        return (
                            <div key={filter.name} className={styles.filterGroup}>
                                <label htmlFor={filter.name}>{filter.label}</label>
                                <select
                                    id={filter.name}
                                    value={values[filter.name] || filter.default}
                                    onChange={e => handleChange(filter.name, e.target.value)}
                                    className={styles.selectFilter}
                                >
                                    {filter.options.map(opt => (
                                        <option key={opt.value} value={opt.value}>{opt.label}</option>
                                    ))}
                                </select>
                            </div>
                        );
                    }
                    if (filter.type === 'dateRange') {
                        return (
                            <div key={filter.name} className={styles.filterGroup}>
                                <label>{filter.label}</label>
                                <input
                                    type="date"
                                    value={values[`${filter.name}_from`] || ''}
                                    onChange={e => handleChange(`${filter.name}_from`, e.target.value)}
                                    className={styles.dateInput}
                                />
                                <span>à</span>
                                <input
                                    type="date"
                                    value={values[`${filter.name}_to`] || ''}
                                    onChange={e => handleChange(`${filter.name}_to`, e.target.value)}
                                    className={styles.dateInput}
                                />
                            </div>
                        );
                    }
                    return null;
                })}
                <Button type="submit" customClass={styles.searchButton}>Rechercher</Button>
                <Button
                    type="submit"
                    onClick={handleReset}
                    customClass={styles.resetButton}
                >
                    Réinitialiser
                </Button>
            </form>
        </div>
    );
};

export default GenericFilters;