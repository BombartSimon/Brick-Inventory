<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class RebrickableClient
{
    private string $apiKey;
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->apiKey = $_ENV['REBRICKABLE_API_KEY'];
        $this->client = $client;
    }

    public function getSetInfo(string $setNum): array
    {
        $url = sprintf('https://rebrickable.com/api/v3/lego/sets/%s/', $setNum);

        $response = $this->client->request('GET', $url, [
            'headers' => [
                'Authorization' => 'key ' . $this->apiKey,
            ],
        ]);

        return $response->toArray();
    }

    public function getSetParts(string $setNum): array
    {
        $url = sprintf('https://rebrickable.com/api/v3/lego/sets/%s/parts/?page=1&page_size=100000&inc_minifig_parts=1', $setNum);

        $response = $this->client->request('GET', $url, [
            'headers' => [
                'Authorization' => 'key ' . $this->apiKey,
            ],
        ]);

        return $response->toArray();
    }
}
