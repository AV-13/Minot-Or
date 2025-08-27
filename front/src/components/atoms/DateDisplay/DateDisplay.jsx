// front/src/components/atoms/DateDisplay/DateDisplay.jsx
import React from 'react';
import styles from './DateDisplay.module.scss';

const DateDisplay = ({ date, format = 'long' }) => {
    if (!date) return <span className={styles.noDate}>Non définie</span>;

    const dateObj = new Date(date);

    if (format === 'long') {
        return <span className={styles.date}>
      {dateObj.toLocaleDateString('fr-FR', {
          day: 'numeric',
          month: 'long',
          year: 'numeric'
      })}
    </span>;
    }

    return <span className={styles.date}>
    {dateObj.toLocaleDateString('fr-FR')}
  </span>;
};

export default DateDisplay;