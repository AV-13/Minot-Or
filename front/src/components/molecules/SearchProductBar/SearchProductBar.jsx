import React from 'react';
import styles from './SearchProductBar.module.scss';

const SearchProductBar = ({ onSearch }) => {
    return (
        <div className={styles.searchBar}>
            <div className={styles.searchInput}>
                <span className={styles.searchIcon}>🔍</span>
                <input
                    type="text"
                    placeholder="Rechercher un produit..."
                    onChange={(e) => onSearch(e.target.value)}
                />
            </div>
            <div className={styles.filters}>
                <select className={styles.categoryFilter}>
                    <option>Toutes catégories</option>
                </select>
                <select className={styles.supplierFilter}>
                    <option>Tous fournisseurs</option>
                </select>
            </div>
        </div>
    );
};

export default SearchProductBar;