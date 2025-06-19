import React, { useEffect, useState } from 'react';
import DashboardUser from "../organisms/DashboardUser/DashboardUser";
import DashboardProduct from "../organisms/DashboardProduct/DashboardProduct";
import DashboardWarehouse from "../organisms/DashboardWarehouse/DashboardWarehouse";

export default function Dashboard() {

    return (
        <div>
            <DashboardUser />
            <DashboardProduct />
            <DashboardWarehouse />
        </div>
    );
}