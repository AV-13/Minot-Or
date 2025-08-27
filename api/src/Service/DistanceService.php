<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class DistanceService
{
//     public function __construct(
//         private HttpClientInterface $http,
//         private string $baseUrl,   // ex: https://api.route.example
//         private string $apiKey,    // clé API
//     ) {}

    /**
     * Calcule la distance en km entre deux adresses (ou lat/lon).
     * Retourne null si l'API échoue -> tu pourras mettre un fallback.
     */
    public function getDistance(string $fromAddress, string $toAddress): ?float
    {
        try {
            // 1) géocoder si nécessaire
            // 2) appeler l’API de routing
            // 3) parser la réponse et retourner la distance en km

            // Exemple ultra simplifié (à adapter à ton provider)
//             $response = $this->http->request('GET', $this->baseUrl.'/route', [
//                 'query' => [
//                     'from' => $fromAddress,
//                     'to'   => $toAddress,
//                     'key'  => $this->apiKey,
//                 ],
//                 'timeout' => 5,
//             ]);
//
//             $data = $response->toArray();
//             return isset($data['distance_km']) ? (float) $data['distance_km'] : null;
            return 42.0; // mock
        } catch (\Throwable $e) {
            // log si besoin
            return null; // on laisse le caller gérer le fallback
        }
    }
}
