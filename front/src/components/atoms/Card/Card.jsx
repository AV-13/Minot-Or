import React from 'react';
export default function Card({ children }) {
    return <div style={{ border: '1px solid #ccc', borderRadius: 8, padding: 16, margin: 8 }}>{children}</div>;
}