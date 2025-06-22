import React, { useEffect, useState } from 'react';
import DashboardUser from "../../organisms/DashboardUser/DashboardUser";
import DashboardProduct from "../../organisms/DashboardProduct/DashboardProduct";
import DashboardWarehouse from "../../organisms/DashboardWarehouse/DashboardWarehouse";
import MainLayout from "../../templates/MainLayout";
import apiClient from "../../../utils/apiClient";

export default function Dashboard() {

    return (
        <MainLayout>
            <DashboardUser />
            <DashboardProduct />
            <DashboardWarehouse />
        </MainLayout>
    );
}
