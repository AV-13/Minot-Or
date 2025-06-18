import React from 'react';
export default function Select({ options, ...props }) {
    return (
        <select {...props}>
            {options.map(opt => (
                <option key={opt} value={opt}>{opt}</option>
            ))}
        </select>
    );
}