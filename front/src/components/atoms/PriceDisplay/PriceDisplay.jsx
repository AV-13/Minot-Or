// front/src/components/atoms/PriceDisplay/PriceDisplay.jsx
import React from 'react';
import styles from './PriceDisplay.module.scss';

const PriceDisplay = ({ amount, highlight = false }) => {
    return (
        <span className={`${styles.price} ${highlight ? styles.highlight : ''}`}>
      {parseFloat(amount).toLocaleString('fr-FR', {
          style: 'currency',
          currency: 'EUR'
      })}
    </span>
    );
};

export default PriceDisplay;