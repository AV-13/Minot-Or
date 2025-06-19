import React from 'react';
import { useCart } from '../../../contexts/CartContext';
import Header from "../../organisms/Header/Header";
import Footer from "../../organisms/Footer/Footer";
import MainLayout from "../../templates/MainLayout";

export default function Quotation() {
    const { cart, removeFromCart } = useCart();

    console.log("quotation", cart);

    return (
        <MainLayout>
            <Header></Header>
            <h2>Mon Devis</h2>
            {cart.length === 0 ? <p>Aucun produit dans le devis.</p> : (
                <ul>
                    {cart.map(p => (
                        <li key={p.id}>
                            {p.name} <button onClick={() => removeFromCart(p.id)}>Retirer</button>
                        </li>
                    ))}
                </ul>
            )}
            <Footer></Footer>
            {/* Ajoute ici la logique de soumission du devis si besoin */}
        </MainLayout>
    );
}