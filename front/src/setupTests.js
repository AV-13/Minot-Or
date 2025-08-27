import '@testing-library/jest-dom';

// Définir TextEncoder/TextDecoder avant MSW
import { TextEncoder, TextDecoder } from 'util';
global.TextEncoder = TextEncoder;
global.TextDecoder = TextDecoder;

// Puis importer MSW
import { server } from './test/msw/server';

// Démarrage de MSW
beforeAll(() => server.listen({ onUnhandledRequest: 'error' }));
afterEach(() => server.resetHandlers());
afterAll(() => server.close());