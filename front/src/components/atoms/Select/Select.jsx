import React from 'react';
import styles from './Select.module.scss';

export default function Select({
    label,
    options,
    value,
    onChange,
    className = '',
    ...props
}) {
    return (
        <div className={`${styles.inputSelectContainer} ${className}`}>
            {label && <label className={styles.label}>{label}</label>}
            <select
                className={styles.select}
                value={value}
                onChange={onChange}
                {...props}
            >
                {options.map(opt =>
                    typeof opt === 'object' ? (
                        <option key={opt.value} value={opt.value}>{opt.label}</option>
                    ) : (
                        <option key={opt} value={opt}>{opt}</option>
                    )
                )}
            </select>
        </div>
    );
}