// index.js ou App.js
import { BrowserRouter, Routes, Route } from 'react-router';
import { AuthProvider } from './contexts/AuthContext';
import { RequireAuth } from './components/auth/RequireAuth';
import Home from './components/pages/Home/Home';
import Dashboard from './components/pages/Dashboard';
// import AdminPanel from './components/pages/AdminPanel';
import Login from './components/pages/Login/Login';
import Register from './components/pages/Register/Register';
import Product from "./components/pages/Product/Product";
// import Unauthorized from './components/pages/Unauthorized';

function App() {
    return (
        <AuthProvider>
            <BrowserRouter>
                <Routes>
                    <Route path="/" element={<Home />} />
                    <Route path="/login" element={<Login />} />
                    <Route path="/register" element={<Register />} />
                    {/*CHECK ROLE POUR ACCEDER A PRODUCT*/}
                    <Route path="/product" element={<Product />} />
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
                        path="/admin"
                        element={
                            <RequireAuth allowedRoles={['Sales']}>
                                {/*<AdminPanel />*/}
                            </RequireAuth>
                        }
                    />
                </Routes>
            </BrowserRouter>
        </AuthProvider>
    );
}

export default App;
