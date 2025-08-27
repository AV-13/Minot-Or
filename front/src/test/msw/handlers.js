// test/msw/handlers.js
import { http, HttpResponse } from 'msw';

export const calls = {
  authHeader: null,
  salesListBody: null,
  containsBodies: [],
  deliveryBody: null,
  quotationBody: null,
  evaluatesBody: null,
};

export const handlers = [
  // 1) POST /salesLists
  http.post('http://localhost:8000/api/salesLists', async ({ request }) => {
    calls.authHeader = request.headers.get('authorization'); // pour vérifier le token
    calls.salesListBody = await request.json();
    return HttpResponse.json({ id: 999 }); // <-- id de la salesList
  }),

  // 2) POST /contains (une fois par item du panier)
  http.post('http://localhost:8000/api/contains', async ({ request }) => {
    calls.containsBodies.push(await request.json());
    return HttpResponse.json({ ok: true });
  }),

  // 3) POST /deliveries/salesLists/:id/delivery
  http.post('http://localhost:8000/api/deliveries/salesLists/:id/delivery', async ({ request }) => {
    calls.deliveryBody = await request.json();
    return HttpResponse.json({ ok: true });
  }),

  // 4) POST /quotations/salesLists/:id/quotation
  http.post('http://localhost:8000/api/quotations/salesLists/:id/quotation', async ({ request }) => {
    calls.quotationBody = await request.json();
    return HttpResponse.json({ ok: true });
  }),

  // 5) GET /users/me
  http.get('http://localhost:8000/api/users/me', () => {
    return HttpResponse.json({ id: 42, email: 'jean@example.com' });
  }),

  // 6) POST /evaluates
  http.post('http://localhost:8000/api/evaluates', async ({ request }) => {
    calls.evaluatesBody = await request.json();
    return HttpResponse.json({ ok: true });
  }),
];
