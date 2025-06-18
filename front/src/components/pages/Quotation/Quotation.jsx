import React from 'react';
import { useCart } from '../../../contexts/CartContext';

export default function Quotation() {
    const { cart, removeFromCart } = useCart();

    console.log("quotation", cart);

    return (
        <div>
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
            {/* Ajoute ici la logique de soumission du devis si besoin */}
        </div>
    );
}