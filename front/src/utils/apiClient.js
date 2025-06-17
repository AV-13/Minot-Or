import axios from 'axios';

// Configuration de base d'axios
const apiClient = axios.create({
    baseURL: 'http://127.0.0.1:8000/api', // Remplacez par l'URL de base de votre API
    headers: {
        'Content-Type': 'application/json',
    },
    timeout: 10000, // Timeout de 10 secondes
});

// Interceptor pour les requêtes (ajout de tokens, logs, etc.)
apiClient.interceptors.request.use(
    (config) => {
        // Exemple : Ajouter un token d'authentification si nécessaire
        const token = localStorage.getItem('token'); // Remplacez par votre méthode de stockage
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        // Gestion des erreurs de requête
        return Promise.reject(error);
    }
);

// Interceptor pour les réponses (gestion des erreurs globales)
apiClient.interceptors.response.use(
    (response) => {
        return response.data; // Retourne directement les données de la réponse
    },
    (error) => {
        // Gestion des erreurs (par exemple, affichage d'un message global)
        if (error.response) {
            console.error('Erreur API :', error.response.data.message || error.message);
        } else {
            console.error('Erreur réseau ou timeout :', error.message);
        }
        return Promise.reject(error);
    }
);

export default apiClient;