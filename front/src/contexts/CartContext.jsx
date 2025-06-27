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

    const addToCart = (product, quantityToAdd = 1) => {
        setCart(prevCart => {
            const existing = prevCart.find(p => p.id === product.id);

            if (existing) {
                // Si le produit existe déjà, on incrémente la quantité de 1 (ou de la valeur spécifiée)
                return prevCart.map(p =>
                    p.id === product.id
                        ? { ...p, quantity: p.quantity + quantityToAdd }
                        : p
                );
            } else {
                // Si le produit n'existe pas encore, on l'ajoute avec quantité = 1 (ou valeur spécifiée)
                return [...prevCart, {
                    ...product,
                    quantity: quantityToAdd
                }];
            }
        });
    };

    const removeFromCart = (id) => {
        setCart(cart.filter(p => p.id !== id));
    };

    const updateQuantity = (productId, newQuantity) => {
        setCart(prevCart => {
            return prevCart.map(item =>
                item.id === productId
                    ? { ...item, quantity: newQuantity }
                    : item
            );
        });
    };

    return (
        <CartContext.Provider value={{ cart, addToCart, removeFromCart, updateQuantity }}>
            {children}
        </CartContext.Provider>
    );
}

export function useCart() {
    return useContext(CartContext);
}