import React, { useEffect, useState } from 'react';
import apiClient from "../../utils/apiClient";

const DebuggingComponent = () => {
    const [results, setResults] = useState({});

    useEffect(() => {
        const token = localStorage.getItem('token');
        console.log("Token stocké:", token ? "Présent" : "Absent",
            token ? token.substring(0, 20) + "..." : "");

        const testAuth = async () => {
            // Test avec fetch standard
            try {
                const fetchResponse = await fetch('http://localhost:8000/api/products', {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                });
                const fetchData = await fetchResponse.json();
                console.log("Fetch - Statut:", fetchResponse.status);
                console.log("Fetch - Réponse:", fetchData);
                setResults(prev => ({...prev, fetch: {status: fetchResponse.status, data: fetchData}}));
            } catch (error) {
                console.error("Fetch - Erreur:", error);
            }

            // Test avec apiClient
            try {
                const apiResponse = await apiClient.get('/products');
                console.log("ApiClient - Statut: 200");
                console.log("ApiClient - Réponse:", apiResponse);
                setResults(prev => ({...prev, apiClient: {status: 200, data: apiResponse}}));
            } catch (error) {
                console.error("ApiClient - Erreur:", error.response?.status || error.message);
                setResults(prev => ({...prev, apiClient: {status: error.response?.status, error: error.message}}));
            }
        };

        testAuth();
    }, []);

    return (
        <div>
            <h2>Débogage en cours, vérifiez la console...</h2>
            <pre>{JSON.stringify(results, null, 2)}</pre>
        </div>
    );
};

export default DebuggingComponent;