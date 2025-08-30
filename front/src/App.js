// front/src/App.js
import { BrowserRouter, Routes, Route } from 'react-router';
import { AuthProvider, useAuth } from './contexts/AuthContext'
import { RequireAuth } from './components/auth/RequireAuth';
import Home from './components/pages/Home/Home';
import Dashboard from './components/pages/Dashboard/Dashboard';
import Login from './components/pages/Login/Login';
import Register from './components/pages/Register/Register';
import Product from "./components/pages/Product/Product";
import Quotation from "./components/pages/Quotation/Quotation";
import { CartProvider } from "./contexts/CartContext";
import QuotationDetail from "./components/pages/QuotationDetail/QuotationDetail";
import DashboardWarehouses from "./components/pages/DashboardWarehouses/DashboardWarehouses";
import DashboardQuotations from "./components/pages/DashboardQuotations/DashboardQuotations";
import DashboardProducts from "./components/pages/DashboardProducts/DashboardProducts";
import DashboardUsers from "./components/pages/DashboardUsers/DashboardUsers";
import DashboardTrucks from "./components/pages/DashboardTrucks/DashboardTrucks";
import OrderHistory from "./components/pages/OrderHistory/OrderHistory";
import Profile from "./components/pages/Profile/Profile";
import LoadingScreen from './components/atoms/LoadingScreen/LoadingScreen';
import DashboardCompanies from "./components/pages/DashboardCompanies/DashboardCompanies";
import TitleManager from './TitleManager';
import {useAnalytics} from "./hooks/useAnalytics";
import DashboardDeliveries from "./components/pages/DashboardDeliveries/DashboardDeliveries";

// Composant qui contient les routes et qui vérifie l'état de chargement
const AppRoutes = () => {
    const { isLoading } = useAuth();
    useAnalytics();
    if (isLoading) {
        return <LoadingScreen />;
    }

    return (
        <Routes>
            <Route path="/" element={<Home />} />
            <Route path="/login" element={<Login />} />
            <Route path="/register" element={<Register />} />
            <Route path="/product" element={<Product />} />
            <Route path="/quotation" element={<Quotation />} />

            {/* Routes protégées par rôle */}
            <Route
                path="/dashboard"
                element={
                    <RequireAuth allowedRoles={['admin', 'Sales', 'Driver']}>
                        <Dashboard />
                    </RequireAuth>
                }
            />
            <Route
                path="/dashboard/users"
                element={
                    <RequireAuth allowedRoles={['admin', 'Sales', 'Driver']}>
                        <DashboardUsers />
                    </RequireAuth>
                }
            />
            <Route
                path="/dashboard/products"
                element={
                    <RequireAuth allowedRoles={['admin', 'Sales', 'Driver']}>
                        <DashboardProducts />
                    </RequireAuth>
                }
            />
            <Route
                path="/dashboard/warehouses"
                element={
                    <RequireAuth allowedRoles={['admin', 'Sales', 'Driver']}>
                        <DashboardWarehouses />
                    </RequireAuth>
                }
            />
            <Route
                path="/dashboard/quotations"
                element={
                    <RequireAuth allowedRoles={['admin', 'Sales', 'Driver']}>
                        <DashboardQuotations />
                    </RequireAuth>
                }
            />
            <Route
                path="/dashboard/trucks"
                element={
                    <RequireAuth allowedRoles={['admin', 'Sales', 'Driver']}>
                        <DashboardTrucks />
                    </RequireAuth>
                }
            />
            <Route path="/quotation/detail/:id" element={<QuotationDetail />} />
            <Route
                path="/order-history"
                element={
                    <RequireAuth allowedRoles={['user']}>
                        <OrderHistory />
                    </RequireAuth>
                }
            />
            <Route
                path="/profile"
                element={
                    <RequireAuth>
                        <Profile />
                    </RequireAuth>
                }
            />
            <Route
                path="/dashboard/companies"
                element={
                    <RequireAuth allowedRoles={['admin', 'Sales']}>
                        <DashboardCompanies />
                    </RequireAuth>
                }
            />
            <Route
                path="/dashboard/deliveries"
                element={
                    <RequireAuth allowedRoles={['admin', 'Sales', 'Driver']}>
                        <DashboardDeliveries />
                    </RequireAuth>
                }
            />
        </Routes>
    );
};

function App() {
    return (
        <BrowserRouter>
          <TitleManager />
          <AuthProvider>
            <CartProvider>
              <AppRoutes />
            </CartProvider>
          </AuthProvider>
        </BrowserRouter>
    );
}

export default App;