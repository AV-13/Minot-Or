import React from 'react';
import {needsCleaning} from "../../services/truckCleaningService";
// import DateLabel from '../DateLabel/DateLabel';
import TruckStatusBadge from "../atoms/TruckStatusBadge/TruckStatusBadge";

export default function TruckRow({ truck }) {
    const alert = needsCleaning(truck.lastCleaningDate);
    return (
        <tr>
            <td>{truck.name}</td>
            <td>{truck.lastCleaningDate}</td>
            <td><TruckStatusBadge alert={alert} /></td>
        </tr>
    );
}