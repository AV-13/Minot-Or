// src/__tests__/Quotation.integration.test.jsx
import React from 'react';

import { TextEncoder, TextDecoder } from 'util';
global.TextEncoder = TextEncoder;
global.TextDecoder = TextDecoder;

import { render, screen, fireEvent, waitFor } from '@testing-library/react';
import Quotation from '../components/pages/Quotation/Quotation';
import { server } from '../test/msw/server';
import { http, HttpResponse } from 'msw';
import { calls } from '../test/msw/handlers';

// ---------- Mocks globaux (OK pour jest.mock : noms commençant par "mock") ----------
const mockNavigate = jest.fn();
const mockClearCart = jest.fn();

// Mock du router (useNavigate)
jest.mock('react-router', () => {
  const actual = jest.requireActual('react-router');
  return {
    ...actual,
    useNavigate: () => mockNavigate, // ✅ pas de window ici
  };
});

// Mock du CartContext (le vrai composant Quotation utilise useCart)
jest.mock('../contexts/CartContext', () => {
  const cart = [
    { id: 1, name: 'Produit A', grossPrice: 10, quantity: 3 }, // total 30
    { id: 2, name: 'Produit B', grossPrice: 20, quantity: 2 }, // total 40
  ];
  return {
    useCart: () => ({
      cart,
      removeFromCart: jest.fn(),
      updateQuantity: jest.fn(),
      clearCart: mockClearCart, // ✅ mock traçable
    }),
  };
});

// Mocks très simples des enfants UI (pour piloter le test sans dépendre de leur DOM réel)
jest.mock('../components/organisms/Header/Header', () => () => <div>Header</div>);
jest.mock('../components/organisms/Footer/Footer', () => () => <div>Footer</div>);
jest.mock('../components/templates/MainLayout', () => ({ children }) => <div>{children}</div>);

jest.mock(
  '../components/organisms/ProductSelectionSection/ProductSelectionSection',
  () =>
    ({ cart }) => <div data-testid="product-section">Produits: {cart.length}</div>
);

jest.mock(
  '../components/organisms/DeliveryInfoSection/DeliveryInfoSection',
  () =>
    ({ onInfoChange }) => (
      <div>
        <input
          aria-label="date"
          type="date"
          onChange={(e) => onInfoChange('deliveryDate', e.target.value)}
        />
        <input
          aria-label="address"
          type="text"
          onChange={(e) => onInfoChange('address', e.target.value)}
        />
      </div>
    )
);

jest.mock(
  '../components/organisms/QuotationSummary/QuotationSummary',
  () =>
    ({ onSubmitQuotation, subtotal, vat, total }) => (
      <div>
        <div>Subtotal:{subtotal}</div>
        <div>VAT:{vat}</div>
        <div>Total:{total}</div>
        <button onClick={onSubmitQuotation}>Soumettre</button>
      </div>
    )
);

// ---------- Tests ----------
describe('Quotation – test d’intégration front avec apiClient + MSW', () => {
  beforeEach(() => {
    // token pour l’interceptor axios
    localStorage.setItem('token', 'test-token-123');

    // reset des mocks
    mockNavigate.mockReset();
    mockClearCart.mockReset();

    // reset du state des handlers MSW (où on stocke les payloads)
    calls.authHeader = null;
    calls.salesListBody = null;
    calls.containsBodies = [];
    calls.deliveryBody = null;
    calls.quotationBody = null;
    calls.evaluatesBody = null;

    // evite les popups réelles
    jest.spyOn(window, 'alert').mockImplementation(() => {});
  });

  afterEach(() => {
    localStorage.clear();
    jest.restoreAllMocks();
  });

  test('enchaîne les appels API, envoie le token et navigue vers le détail', async () => {
    render(<Quotation />);

    // Renseigne les infos de livraison
    fireEvent.change(screen.getByLabelText('date'), {
      target: { value: '2025-09-01' },
    });
    fireEvent.change(screen.getByLabelText('address'), {
      target: { value: '12 rue Test' },
    });

    // Soumission
    fireEvent.click(screen.getByText('Soumettre'));

    // La navigation doit être appelée avec /quotation/detail/999 (ID renvoyé par le handler /salesLists)
    await waitFor(() => {
      expect(mockNavigate).toHaveBeenCalled();
    });
    const [navPath] = mockNavigate.mock.calls[0];
    expect(navPath).toMatch(/^\/quotation\/detail\/999$/);

    // Vérifie le header Authorization injecté par l’interceptor axios
    expect(calls.authHeader).toBe('Bearer test-token-123');

    // Vérifie le body de /salesLists : productsPrice = 70 (30 + 40)
    expect(calls.salesListBody).toMatchObject({
      status: 'pending',
      productsPrice: 70,
      globalDiscount: 0,
    });
    expect(calls.salesListBody).toHaveProperty('issueDate');
    expect(calls.salesListBody).toHaveProperty('expirationDate');
    expect(calls.salesListBody).toHaveProperty('orderDate');

    // Vérifie que chaque produit du panier a été posté dans /contains
    expect(calls.containsBodies).toHaveLength(2);
    expect(calls.containsBodies).toEqual(
      expect.arrayContaining([
        expect.objectContaining({
          salesListId: 999,
          productId: 1,
          productQuantity: 3,
          productDiscount: 0,
        }),
        expect.objectContaining({
          salesListId: 999,
          productId: 2,
          productQuantity: 2,
          productDiscount: 0,
        }),
      ])
    );

    // Vérifie la création de la livraison
    expect(calls.deliveryBody).toMatchObject({
      deliveryDate: '2025-09-01',
      deliveryAddress: '12 rue Test',
      deliveryStatus: 'InPreparation',
    });

    // Vérifie la création du devis
    expect(calls.quotationBody).toHaveProperty('dueDate'); // expirationDate J+30
    expect(typeof calls.quotationBody.distance).toBe('number');

    // Vérifie evaluates (liaison user/salesList)
    expect(calls.evaluatesBody).toMatchObject({
      salesListId: 999,
      userId: 42,
      quoteAccepted: false,
    });

    // Le panier a été vidé
    expect(mockClearCart).toHaveBeenCalled();
  });

  test('affiche une alerte si /salesLists renvoie 500 et ne navigue pas', async () => {
    // Override MSW pour simuler une 500 sur la première étape
    server.use(
      http.post('http://localhost:8000/api/salesLists', () =>
        HttpResponse.json({ message: 'Erreur interne' }, { status: 500 })
      )
    );

    render(<Quotation />);

    fireEvent.change(screen.getByLabelText('date'), {
      target: { value: '2025-09-01' },
    });
    fireEvent.change(screen.getByLabelText('address'), {
      target: { value: '12 rue Test' },
    });
    fireEvent.click(screen.getByText('Soumettre'));

    await waitFor(() => {
      expect(window.alert).toHaveBeenCalledWith(
        'Une erreur est survenue lors de la création du devis. Veuillez réessayer.'
      );
    });

    // Pas de navigation en cas d’erreur
    expect(mockNavigate).not.toHaveBeenCalled();
    // Et pas de clearCart
    expect(mockClearCart).not.toHaveBeenCalled();
  });
});
