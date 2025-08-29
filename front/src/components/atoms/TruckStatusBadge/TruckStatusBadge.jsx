import React from 'react';

export default function TruckStatusBadge({ alert }) {
    return (
        <span style={{ color: alert ? 'red' : 'green' }}>
            {alert ? 'À laver' : 'OK'}
        </span>
    );
}