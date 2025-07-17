// index.js ou App.js
import { BrowserRouter, Routes, Route } from 'react-router';
import { AuthProvider } from './contexts/AuthContext';
import { RequireAuth } from './components/auth/RequireAuth';
import Home from './components/pages/Home/Home';
import Dashboard from './components/pages/Dashboard/Dashboard';
import Login from './components/pages/Login/Login';
import Register from './components/pages/Register/Register';
import Product from "./components/pages/Product/Product";
import Quotation from "./components/pages/Quotation/Quotation";
import {CartProvider} from "./contexts/CartContext";
import QuotationDetail from "./components/pages/QuotationDetail/QuotationDetail";
import DashboardWarehouses from "./components/pages/DashboardWarehouses/DashboardWarehouses";
import DashboardQuotations from "./components/pages/DashboardQuotations/DashboardQuotations";
import DashboardProducts from "./components/pages/DashboardProducts/DashboardProducts";
import DashboardUsers from "./components/pages/DashboardUsers/DashboardUsers";
import OrderHistory from "./components/pages/OrderHistory/OrderHistory";
// import Unauthorized from './components/pages/Unauthorized';

function App() {
    return (
        <BrowserRouter>
            <AuthProvider>
                <CartProvider>
                        <Routes>
                            <Route path="/" element={<Home />} />
                            <Route path="/login" element={<Login />} />
                            <Route path="/register" element={<Register />} />
                            {/*CHECK ROLE POUR ACCEDER A PRODUCT*/}
                            <Route path="/product" element={<Product />} />
                            <Route path="/quotation" element={<Quotation />} />
                            {/*<Route path="/unauthorized" element={<Unauthorized />} />*/}

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
                            <Route path="/quotation/detail/:id" element={<QuotationDetail />} />
                            <Route
                                path="/order-history"
                                element={
                                    <RequireAuth allowedRoles={['user']}>
                                        <OrderHistory />
                                    </RequireAuth>
                                }
                            />
                        </Routes>
                </CartProvider>
            </AuthProvider>
        </BrowserRouter>
    );
}

export default App;
