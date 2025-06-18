import React, { createContext, useContext, useState, useEffect } from 'react';

const CartContext = createContext();

export function CartProvider({ children }) {
    const [cart, setCart] = useState(() => {
        const stored = localStorage.getItem('cart');
        return stored ? JSON.parse(stored) : [];
    });

    useEffect(() => {
        localStorage.setItem('cart', JSON.stringify(cart));
    }, [cart]);

    const addToCart = (product) => {
        const step = product.quantity || 1; // ou product.stepQuantity selon ton modèle
        setCart(prevCart => {
            const existing = prevCart.find(p => p.id === product.id);
            if (existing) {
                return prevCart.map(p =>
                    p.id === product.id
                        ? { ...p, quantity: (p.quantity || step) + step }
                        : p
                );
            } else {
                return [...prevCart, { ...product, quantity: step }];
            }
        });
    };

    const removeFromCart = (id) => {
        setCart(cart.filter(p => p.id !== id));
    };

    return (
        <CartContext.Provider value={{ cart, addToCart, removeFromCart }}>
            {children}
        </CartContext.Provider>
    );
}

export function useCart() {
    return useContext(CartContext);
}