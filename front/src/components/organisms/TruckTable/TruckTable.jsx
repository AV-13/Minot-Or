// front/src/components/organisms/TruckTable/TruckTable.jsx
import React from 'react';
import TruckRow from "../../molecules/TruckRow";

export default function TruckTable({ trucks }) {
    return (
        <table>
            <thead>
                <tr>
                    <th>Camion</th>
                    <th>Dernier nettoyage</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                {trucks.map(truck => (
                    <TruckRow key={truck.id} truck={truck} />
                ))}
            </tbody>
        </table>
    );
}