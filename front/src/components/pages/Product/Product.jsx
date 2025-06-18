import React, {useState, useMemo, useEffect} from 'react';
import ProductFilter from '../../molecules/ProductFilter/ProductFilter';
import ProductList from '../../organisms/ProductList/ProductList';
import apiClient from "../../../utils/apiClient";
import { TYPES } from "../../../constants/productType";
import { useCart } from '../../../contexts/CartContext';


export default function Product() {
    const [name, setName] = useState('');
    const [type, setType] = useState('');
    const [loading, setLoading] = useState(true);
    const [products, setProducts] = useState([]);
    const { addToCart } = useCart();

    useEffect(() => { fetchProduct(); }, []);

    const fetchProduct = async () => {
        setLoading(true);
        try {
            const res = await apiClient.get('/products');
            setProducts(Array.isArray(res.items) ? res.items : []);
        } catch (e) {
            setProducts([]);
        }
        setLoading(false);
    };

    const filteredProduct = useMemo(() =>
        products.filter(p =>
            (!name || p.name.toLowerCase().includes(name.toLowerCase())) &&
            (!type || p.category === type)
        ), [name, type, products]
    );

    return (
        <div>
            <h2>Produits</h2>
            <ProductFilter
                name={name}
                type={type}
                onNameChange={setName}
                onTypeChange={setType}
                types={TYPES}
            />
            <ProductList products={filteredProduct} onAddToCart={addToCart} />
        </div>
    );
}