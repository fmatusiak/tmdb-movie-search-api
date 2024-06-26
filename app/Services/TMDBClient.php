<?php

namespace App\Services;

use App\Exceptions\TMDBApiException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Config;

class TMDBClient
{
    private string $apiKey;
    private Client $client;

    /**
     * @throws TMDBApiException
     */
    public function __construct(Client $client)
    {
        $this->apiKey = $this->getApiKey();
        $this->client = $client;
    }

    /**
     * @throws TMDBApiException
     */
    private function getApiKey(): string
    {
        $apiKey = Config::get('tmdb.TMDB_API_KEY');

        if (!$apiKey) {
            throw new TMDBApiException('TMDB_API_KEY is not set in the tmdb configuration');
        }

        return $apiKey;
    }

    public function prepareQueryParams(string $language, int $page = null): array
    {
        $queryParams['api_key'] = $this->apiKey;
        $queryParams['language'] = $language;

        if ($page) {
            $queryParams['page'] = $page;
        }

        return $queryParams;
    }

    /**
     * @throws TMDBApiException
     */
    public function getRequest(string $url, array $queryParams = []): array
    {
        try {
            $response = $this->client->request('GET', $url, [
                'query' => $queryParams
            ]);

            $body = $response->getBody()->getContents();
            $responseData = json_decode($body, true);

            if (!$responseData && json_last_error() !== JSON_ERROR_NONE) {
                throw new TMDBApiException('Failed to parse JSON response from TMDB API');
            }

            return $responseData;
        } catch (GuzzleException $e) {
            throw new TMDBApiException('Failed to get data from TMDB API', $e->getCode(), $e);
        }
    }
}
