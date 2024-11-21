<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeocodingService
{
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function geocodeAdress(string $address): array
    {
        $response = $this->httpClient->request('GET', 'https://nominatim.openstreetmap.org/search', [
            'query' => [
                'format' => 'json',
                'q' => $address,
                'limit' => 1,
            ],
        ]);

        $data = $response->toArray();

        if (!empty($data)) {
            return [
                'latitude' => (float) $data[0]['lat'],
                'longitude' => (float) $data[0]['lon'],
            ];
        }

        return [];
    }
}